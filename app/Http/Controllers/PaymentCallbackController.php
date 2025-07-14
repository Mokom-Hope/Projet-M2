<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Services\PaymentGatewayService;
use App\Services\TransferNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    protected $paymentGateway;
    protected $notificationService;

    public function __construct(PaymentGatewayService $paymentGateway, TransferNotificationService $notificationService)
    {
        $this->paymentGateway = $paymentGateway;
        $this->notificationService = $notificationService;
    }

    /**
     * Gérer le callback de NotchPay
     */
    public function notchpayCallback(Request $request)
    {
        try {
            Log::info('NotchPay Callback received', $request->all());

            $reference = $request->input('reference');
            $status = $request->input('status');
            $transactionId = $request->input('transaction_id');

            if (!$reference) {
                Log::error('No reference in NotchPay callback');
                return response()->json(['status' => 'error', 'message' => 'No reference provided']);
            }

            // Trouver le transfert
            $transfer = Transfer::where('payment_reference', $reference)->first();

            if (!$transfer) {
                Log::error('Transfer not found for reference: ' . $reference);
                return response()->json(['status' => 'error', 'message' => 'Transfer not found']);
            }

            // Mettre à jour le transfert selon le statut
            switch ($status) {
                case 'completed':
                case 'success':
                    $transfer->update([
                        'status' => 'sent',
                        'payment_completed_at' => now(),
                        'payment_transaction_id' => $transactionId
                    ]);

                    // Envoyer l'email au destinataire
                    $this->notificationService->sendTransferNotification($transfer);

                    Log::info('Transfer completed and email sent', ['transfer_id' => $transfer->id]);
                    break;

                case 'failed':
                case 'cancelled':
                    $transfer->update([
                        'status' => 'failed',
                        'failure_reason' => "Payment {$status} via NotchPay callback"
                    ]);

                    Log::info('Transfer marked as failed', ['transfer_id' => $transfer->id]);
                    break;

                default:
                    Log::info('Transfer status updated', [
                        'transfer_id' => $transfer->id,
                        'status' => $status
                    ]);
                    break;
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('NotchPay callback error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal error']);
        }
    }

    /**
     * Gérer le retour de NotchPay (quand l'utilisateur revient)
     */
    public function paymentReturn(Request $request)
    {
        try {
            $reference = $request->input('reference');
            $status = $request->input('status');

            Log::info('NotchPay Return received', [
                'reference' => $reference,
                'status' => $status,
                'all_params' => $request->all()
            ]);

            if (!$reference) {
                return redirect()->route('dashboard')
                    ->with('error', 'Référence de paiement manquante');
            }

            // Trouver le transfert
            $transfer = Transfer::where('payment_reference', $reference)->first();

            if (!$transfer) {
                return redirect()->route('dashboard')
                    ->with('error', 'Transfert non trouvé');
            }

            // Vérifier le statut auprès de NotchPay
            $verificationResult = $this->paymentGateway->verifyPayment($reference);

            if ($verificationResult['success']) {
                $paymentStatus = $verificationResult['status'];

                switch ($paymentStatus) {
                    case 'completed':
                    case 'success':
                        $transfer->update([
                            'status' => 'sent',
                            'payment_completed_at' => now()
                        ]);

                        // Envoyer l'email au destinataire
                        $this->notificationService->sendTransferNotification($transfer);

                        return redirect()->route('transfers.show', $transfer)
                            ->with('success', 'Paiement confirmé ! Email envoyé au destinataire.');

                    case 'failed':
                    case 'cancelled':
                        $transfer->update([
                            'status' => 'failed',
                            'failure_reason' => "Payment {$paymentStatus} on return"
                        ]);

                        return redirect()->route('transfers.show', $transfer)
                            ->with('error', 'Le paiement a échoué. Veuillez réessayer.');

                    default:
                        return redirect()->route('transfers.show', $transfer)
                            ->with('info', 'Paiement en cours de traitement. Nous vous notifierons dès confirmation.');
                }
            } else {
                return redirect()->route('transfers.show', $transfer)
                    ->with('warning', 'Impossible de vérifier le statut du paiement. Veuillez vérifier manuellement.');
            }

        } catch (\Exception $e) {
            Log::error('Payment return error: ' . $e->getMessage());
            
            return redirect()->route('dashboard')
                ->with('error', 'Une erreur est survenue lors du traitement du retour de paiement');
        }
    }
}
