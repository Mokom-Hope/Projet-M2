<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function notchpayCallback(Request $request)
    {
        try {
            Log::info('NotchPay Callback received', $request->all());

            // Récupérer la référence du paiement
            $reference = $request->get('reference');
            if (!$reference) {
                Log::error('Callback NotchPay: Référence manquante');
                return redirect()->route('dashboard')->with('error', 'Une erreur est survenue lors du traitement du paiement.');
            }

            // Récupérer le transfert dans notre base de données
            $transfer = Transfer::where('payment_reference', $reference)->first();
            
            if (!$transfer) {
                Log::error('Callback NotchPay: Transfert non trouvé pour la référence ' . $reference);
                return redirect()->route('dashboard')->with('error', 'Transfert non trouvé.');
            }

            // Vérifier le statut du paiement auprès de NotchPay
            $gatewayService = app(\App\Services\PaymentGatewayService::class);
            $result = $gatewayService->verifyPayment($reference);
            
            if (!$result['success']) {
                Log::error('Erreur lors de la vérification du paiement: ' . ($result['message'] ?? 'Erreur inconnue'));
                return redirect()->route('transfers.show', $transfer)->with('error', 'Erreur lors de la vérification du paiement.');
            }
            
            // Mettre à jour le statut du transfert selon le résultat
            $paymentStatus = $result['status'];
            
            switch ($paymentStatus) {
                case 'completed':
                    $transfer->update([
                        'status' => 'sent',
                        'payment_completed_at' => now()
                    ]);
                    Log::info('Payment completed for transfer: ' . $transfer->id);
                    return redirect()->route('transfers.show', $transfer)->with('success', 'Transfert envoyé avec succès !');

                case 'canceled':
                    $transfer->update(['status' => 'cancelled']);
                    Log::info('Payment cancelled for transfer: ' . $transfer->id);
                    return redirect()->route('transfers.show', $transfer)->with('error', 'Le paiement a été annulé.');

                default:
                    $transfer->update([
                        'status' => 'failed',
                        'failure_reason' => 'Payment status: ' . $paymentStatus
                    ]);
                    Log::info('Payment failed for transfer: ' . $transfer->id . ', status: ' . $paymentStatus);
                    return redirect()->route('transfers.show', $transfer)->with('error', 'Le paiement a échoué. Veuillez réessayer.');
            }

        } catch (\Exception $e) {
            Log::error('Callback processing error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Une erreur est survenue lors du traitement du paiement.');
        }
    }

    public function paymentReturn(Request $request)
    {
        $reference = $request->get('reference');
        
        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'Référence de paiement manquante');
        }

        $transfer = Transfer::where('payment_reference', $reference)->first();

        if (!$transfer) {
            return redirect()->route('dashboard')->with('error', 'Transfert non trouvé');
        }

        return redirect()->route('transfers.show', $transfer);
    }
}
