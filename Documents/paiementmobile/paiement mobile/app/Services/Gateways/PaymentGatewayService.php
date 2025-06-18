<?php

namespace App\Services;

use App\Services\Gateways\NotchPayGateway;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected $notchPayGateway;

    public function __construct(NotchPayGateway $notchPayGateway)
    {
        $this->notchPayGateway = $notchPayGateway;
    }

    public function processPayment(PaymentMethod $paymentMethod, float $amount, string $currency, int $transferId): array
    {
        try {
            // Générer une référence unique
            $reference = 'TRANSFER_' . $transferId . '_' . time();
            
            // Préparer les données pour NotchPay
            $paymentData = [
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference,
                'description' => 'Transfert d\'argent #' . $transferId,
                'transfer_id' => $transferId,
                'payment_method_id' => $paymentMethod->id,
                'customer' => [
                    'email' => $paymentMethod->user->email,
                    'phone' => $paymentMethod->user->phone ?? $paymentMethod->account_number,
                    'name' => $paymentMethod->user->full_name
                ]
            ];

            Log::info('Processing payment via NotchPay', $paymentData);

            // Traiter le paiement via NotchPay
            return $this->notchPayGateway->charge($paymentData);

        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors du traitement du paiement: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function verifyPayment(string $reference): array
    {
        try {
            return $this->notchPayGateway->verifyTransaction($reference);
        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors de la vérification du paiement',
                'data' => null
            ];
        }
    }
}
