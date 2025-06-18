<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Services\Gateways\NotchPayGateway;

class PaymentGatewayService
{
    private array $gateways;

    public function __construct()
    {
        $this->gateways = [
            'notchpay' => new NotchPayGateway(),
        ];
    }

    public function processPayment(
        PaymentMethod $paymentMethod,
        float $amount,
        string $currency,
        int $transferId
    ): array {
        
        $gateway = $this->getGateway('notchpay');
        
        return $gateway->charge([
            'amount' => $amount,
            'currency' => $currency,
            'reference' => "TRANSFER_{$transferId}_" . time(),
            'description' => "Transfert d'argent #{$transferId}",
            'transfer_id' => $transferId,
            'payment_method_id' => $paymentMethod->id,
            'customer' => [
                'email' => $paymentMethod->user->email,
                'phone' => $paymentMethod->account_number,
                'name' => $paymentMethod->account_name,
            ]
        ]);
    }

    public function sendToRecipient(
        int $recipientPaymentMethodId,
        float $amount,
        string $currency,
        int $transferId
    ): array {
        
        $paymentMethod = PaymentMethod::findOrFail($recipientPaymentMethodId);
        $gateway = $this->getGateway('notchpay');
        
        return $gateway->transfer([
            'amount' => $amount,
            'currency' => $currency,
            'reference' => "PAYOUT_{$transferId}_" . time(),
            'description' => "Réception transfert #{$transferId}",
            'transfer_id' => $transferId,
            'recipient' => [
                'type' => $paymentMethod->type,
                'provider' => $paymentMethod->provider,
                'account_number' => $paymentMethod->account_number,
                'account_name' => $paymentMethod->account_name,
            ]
        ]);
    }

    public function getSupportedMethods(string $country = 'CM'): array
    {
        $gateway = $this->getGateway('notchpay');
        return $gateway->getSupportedMethods($country);
    }

    public function verifyTransaction(string $reference): array
    {
        $gateway = $this->getGateway('notchpay');
        return $gateway->verifyTransaction($reference);
    }

    public function createPaymentLink(array $data): array
    {
        $gateway = $this->getGateway('notchpay');
        return $gateway->createPaymentLink($data);
    }

    private function getGateway(string $provider = 'notchpay')
    {
        return $this->gateways['notchpay'];
    }
}
