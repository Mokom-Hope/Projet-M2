<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMoneyRequest;
use App\Http\Requests\ClaimMoneyRequest;
use App\Models\Transfer;
use App\Models\PaymentMethod;
use App\Models\Country;
use App\Models\User;
use App\Services\PaymentGatewayService;
use App\Services\TransferNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    protected $paymentGateway;
    protected $notificationService;

    public function __construct(PaymentGatewayService $paymentGateway, TransferNotificationService $notificationService)
    {
        $this->paymentGateway = $paymentGateway;
        $this->notificationService = $notificationService;
    }

    public function create()
    {
        $paymentMethods = Auth::user()->paymentMethods()->where('status', 'active')->get();
        $countries = Country::active()->get();
        
        return view('transfers.create', compact('paymentMethods', 'countries'));
    }

    public function store(SendMoneyRequest $request)
    {
        try {
            DB::beginTransaction();

            $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);
            
            // Vérifier que la méthode appartient à l'utilisateur
            if ($paymentMethod->user_id !== Auth::id()) {
                return back()->withErrors(['payment_method_id' => 'Méthode de paiement invalide']);
            }

            // Calculer les frais
            $fees = $this->calculateFees($request->amount, $request->currency);
            $totalAmount = $request->amount + $fees;

            // Créer le transfert avec statut "pending"
            $transfer = Transfer::create([
                'sender_id' => Auth::id(),
                'recipient_email' => filter_var($request->recipient, FILTER_VALIDATE_EMAIL) ? $request->recipient : null,
                'recipient_phone' => !filter_var($request->recipient, FILTER_VALIDATE_EMAIL) ? $request->recipient : null,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'fees' => $fees,
                'total_amount' => $totalAmount,
                'security_question' => $request->security_question,
                'security_answer_hash' => Hash::make($request->security_answer),
                'payment_method_id' => $paymentMethod->id,
                'notes' => $request->notes,
                'status' => 'pending',
                'transfer_code' => $this->generateTransferCode()
            ]);

            Log::info('Transfer created', ['transfer_id' => $transfer->id]);

            // Traiter le paiement via NotchPay
            $paymentResult = $this->paymentGateway->processPayment(
                $paymentMethod,
                $totalAmount,
                $request->currency,
                $transfer->id
            );

            Log::info('Payment result', $paymentResult);

            if ($paymentResult['success']) {
                // Mettre à jour le transfert avec les infos de paiement
                $transfer->update([
                    'payment_reference' => $paymentResult['reference'],
                    'payment_transaction_id' => $paymentResult['transaction_id'] ?? null,
                    'status' => 'payment_pending'
                ]);

                DB::commit();

                // Rediriger vers NotchPay (page par défaut)
                if (isset($paymentResult['payment_url'])) {
                    return redirect($paymentResult['payment_url']);
                }

                return redirect()->route('transfers.show', $transfer)
                    ->with('success', 'Transfert initié ! Veuillez compléter le paiement.');
            } else {
                $transfer->update(['status' => 'failed']);
                DB::commit();

                return back()->withErrors(['error' => $paymentResult['message'] ?? 'Erreur lors du paiement']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }

    public function history(Request $request)
    {
        $type = $request->get('type', 'all');
        $user = Auth::user();

        $query = Transfer::query();

        switch ($type) {
            case 'sent':
                $query->where('sender_id', $user->id);
                break;
            case 'received':
                $query->where('recipient_id', $user->id);
                break;
            default:
                $query->where(function($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
                });
                break;
        }

        $transfers = $query->with(['sender', 'recipient', 'paymentMethod'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('transfers.history', compact('transfers', 'type'));
    }

    public function show(Transfer $transfer)
    {
        // Vérifier que l'utilisateur peut voir ce transfert
        if ($transfer->sender_id !== Auth::id() && $transfer->recipient_id !== Auth::id()) {
            abort(403);
        }

        return view('transfers.show', compact('transfer'));
    }

    public function showClaimForm()
    {
        $countries = Country::active()->get();
        return view('transfers.claim', compact('countries'));
    }

    public function claim(ClaimMoneyRequest $request)
    {
        try {
            DB::beginTransaction();

            $transfer = Transfer::where('transfer_code', $request->transfer_code)
                              ->where('status', 'sent')
                              ->first();

            if (!$transfer) {
                return back()->withErrors(['transfer_code' => 'Code de transfert invalide ou déjà utilisé']);
            }

            if (!$transfer->canAttemptClaim()) {
                return back()->withErrors(['transfer_code' => 'Ce transfert a expiré ou a atteint le nombre maximum de tentatives']);
            }

            // Vérifier la réponse de sécurité
            if (!Hash::check($request->security_answer, $transfer->security_answer_hash)) {
                $transfer->increment('failed_attempts');
                return back()->withErrors(['security_answer' => 'Réponse de sécurité incorrecte']);
            }

            // Vérifier l'identifiant du destinataire
            $recipientMatch = ($transfer->recipient_email === $request->recipient_identifier) ||
                            ($transfer->recipient_phone === $request->recipient_identifier);

            if (!$recipientMatch) {
                return back()->withErrors(['recipient_identifier' => 'Identifiant du destinataire incorrect']);
            }

            // Créer ou récupérer le compte destinataire
            $recipient = $this->findOrCreateRecipient($request);

            // Créer la méthode de paiement pour le destinataire
            $recipientPaymentMethod = $recipient->paymentMethods()->create([
                'type' => $request->payment_method_type,
                'provider' => $request->payment_method_provider,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'country_code' => Country::find($request->country_id)->code,
                'status' => 'active',
                'is_verified' => false
            ]);

            // Mettre à jour le transfert
            $transfer->update([
                'recipient_id' => $recipient->id,
                'recipient_payment_method_id' => $recipientPaymentMethod->id,
                'status' => 'completed',
                'claimed_at' => now()
            ]);

            DB::commit();

            return redirect()->route('transfers.claim.success', $transfer->transfer_code)
                ->with('success', 'Transfert récupéré avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer claim error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }

    /**
     * CORRECTION PRINCIPALE : Accepter plusieurs statuts pour la vérification du code
     */
    public function getTransferInfo(Request $request)
    {
        $transferCode = $request->get('transfer_code');
        
        Log::info('Checking transfer code', ['code' => $transferCode]);
        
        // Chercher le transfert avec plusieurs statuts possibles
        $transfer = Transfer::where('transfer_code', $transferCode)
                          ->whereIn('status', ['sent', 'payment_pending', 'completed']) // Accepter plusieurs statuts
                          ->with('sender')
                          ->first();

        if (!$transfer) {
            Log::warning('Transfer not found', ['code' => $transferCode]);
            return response()->json([
                'success' => false,
                'message' => 'Code de transfert invalide'
            ]);
        }

        // Vérifier si le transfert a expiré (si la méthode existe)
        if (method_exists($transfer, 'isExpired') && $transfer->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce transfert a expiré'
            ]);
        }

        Log::info('Transfer found', ['transfer_id' => $transfer->id, 'status' => $transfer->status]);

        return response()->json([
            'success' => true,
            'data' => [
                'sender_name' => $transfer->sender->full_name ?? $transfer->sender->first_name . ' ' . $transfer->sender->last_name,
                'amount' => number_format($transfer->amount, 0, ',', ' ') . ' ' . $transfer->currency,
                'security_question' => $transfer->security_question,
                'expires_at' => $transfer->expires_at ? $transfer->expires_at->format('d/m/Y à H:i') : 'Non défini'
            ]
        ]);
    }

    /**
     * Vérifier manuellement le statut d'un transfert
     */
    public function checkPaymentStatus(Transfer $transfer)
    {
        // Vérifier que l'utilisateur peut accéder à ce transfert
        if ($transfer->sender_id !== Auth::id()) {
            abort(403);
        }

        try {
            if (!$transfer->payment_reference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune référence de paiement trouvée'
                ]);
            }

            // Vérifier auprès de NotchPay
            $result = $this->paymentGateway->verifyPayment($transfer->payment_reference);

            if ($result['success']) {
                $status = $result['status'];

                switch ($status) {
                    case 'completed':
                        // Mettre à jour le transfert
                        $transfer->update([
                            'status' => 'sent',
                            'payment_completed_at' => now()
                        ]);

                        // Envoyer l'email au destinataire
                        $this->notificationService->sendTransferNotification($transfer);

                        return response()->json([
                            'success' => true,
                            'status' => 'sent',
                            'message' => 'Paiement confirmé ! Email envoyé au destinataire.'
                        ]);

                    case 'failed':
                    case 'canceled':
                        $transfer->update([
                            'status' => 'failed',
                            'failure_reason' => "Payment {$status} on NotchPay"
                        ]);

                        return response()->json([
                            'success' => false,
                            'status' => 'failed',
                            'message' => "Paiement {$status}. Veuillez réessayer."
                        ]);

                    default:
                        return response()->json([
                            'success' => true,
                            'status' => 'pending',
                            'message' => "Paiement toujours en cours de traitement ({$status})"
                        ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la vérification: ' . $result['message']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Manual payment check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du paiement'
            ]);
        }
    }

    /**
     * Renvoyer la notification email
     */
    public function resendNotification(Transfer $transfer)
    {
        // Vérifier que l'utilisateur peut accéder à ce transfert
        if ($transfer->sender_id !== Auth::id()) {
            abort(403);
        }

        try {
            if ($transfer->status !== 'sent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Le transfert doit être en statut "envoyé" pour renvoyer l\'email'
                ]);
            }

            $result = $this->notificationService->sendTransferNotification($transfer);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email renvoyé avec succès'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi de l\'email'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Resend notification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email'
            ]);
        }
    }

    /**
     * Simuler un paiement réussi pour les tests en local
     */
    public function simulatePaymentSuccess(Transfer $transfer)
    {
        // Vérifier que l'utilisateur peut accéder à ce transfert
        if ($transfer->sender_id !== Auth::id()) {
            abort(403);
        }

        // Seulement en environnement de développement
        if (!app()->environment('local')) {
            abort(404);
        }

        try {
            // Mettre à jour le transfert comme payé
            $transfer->update([
                'status' => 'sent',
                'payment_completed_at' => now()
            ]);

            // Envoyer l'email au destinataire
            $this->notificationService->sendTransferNotification($transfer);

            return redirect()->route('transfers.show', $transfer)
                ->with('success', 'Paiement simulé avec succès ! Email envoyé au destinataire.');

        } catch (\Exception $e) {
            Log::error('Simulate payment error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la simulation du paiement']);
        }
    }

    /**
     * Méthode pour obtenir les méthodes de paiement par pays
     */
    public function getPaymentMethods(Request $request)
    {
        $country = $request->get('country');
        
        $methods = [
            'CM' => [
                ['type' => 'mobile_money', 'provider' => 'Orange Money', 'name' => 'Orange Money'],
                ['type' => 'mobile_money', 'provider' => 'MTN Mobile Money', 'name' => 'MTN Mobile Money'],
                ['type' => 'bank_account', 'provider' => 'Ecobank', 'name' => 'Ecobank'],
                ['type' => 'bank_account', 'provider' => 'UBA', 'name' => 'UBA']
            ],
            'CI' => [
                ['type' => 'mobile_money', 'provider' => 'Orange Money', 'name' => 'Orange Money'],
                ['type' => 'mobile_money', 'provider' => 'MTN Mobile Money', 'name' => 'MTN Mobile Money'],
                ['type' => 'mobile_money', 'provider' => 'Moov Money', 'name' => 'Moov Money'],
                ['type' => 'mobile_money', 'provider' => 'Wave', 'name' => 'Wave'],
                ['type' => 'bank_account', 'provider' => 'SGBCI', 'name' => 'Société Générale']
            ]
        ];

        return response()->json([
            'success' => true,
            'methods' => $methods[$country] ?? []
        ]);
    }

    private function calculateFees($amount, $currency)
    {
        // Logique de calcul des frais
        $feeRate = 0.02; // 2%
        $minFee = $currency === 'XAF' ? 100 : 1;
        $maxFee = $currency === 'XAF' ? 5000 : 50;

        $fee = $amount * $feeRate;
        return max($minFee, min($fee, $maxFee));
    }

    private function findOrCreateRecipient($request)
    {
        $identifier = $request->recipient_identifier;
        
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return User::firstOrCreate(
                ['email' => $identifier],
                [
                    'first_name' => explode('@', $identifier)[0],
                    'last_name' => 'User',
                    'password' => Hash::make('temporary_password'),
                    'country_code' => Country::find($request->country_id)->code,
                    'currency' => 'XAF',
                    'status' => 'active'
                ]
            );
        } else {
            return User::firstOrCreate(
                ['phone' => $identifier],
                [
                    'first_name' => 'User',
                    'last_name' => substr($identifier, -4),
                    'email' => 'user' . time() . '@temp.com',
                    'password' => Hash::make('temporary_password'),
                    'country_code' => Country::find($request->country_id)->code,
                    'currency' => 'XAF',
                    'status' => 'active'
                ]
            );
        }
    }

    private function generateTransferCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (Transfer::where('transfer_code', $code)->exists());

        return $code;
    }
}
