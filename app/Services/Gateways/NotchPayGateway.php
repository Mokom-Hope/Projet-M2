<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotchPayGateway
{
    private string $baseUrl;
    private string $publicKey;
    private string $privateKey;
    private string $environment;

    public function __construct()
    {
        $this->baseUrl = config('payment.notchpay.base_url', 'https://api.notchpay.co/v1');
        $this->publicKey = config('payment.notchpay.public_key');
        $this->privateKey = config('payment.notchpay.private_key');
        $this->environment = config('payment.notchpay.environment', 'sandbox');
    }

    public function charge(array $data): array
    {
        try {
            $payload = [
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'reference' => $data['reference'] ?? 'REF_' . time(),
                'description' => $data['description'] ?? 'Payment',
                'callback' => route('payment.callback'),
                'customer' => [
                    'email' => $data['customer']['email'],
                    'phone' => $data['customer']['phone'],
                    'name' => $data['customer']['name'],
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/payments', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'payment_url' => $data['authorization_url'] ?? null,
                    'reference' => $data['reference'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Erreur de paiement',
                'error' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('NotchPay Charge Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de connexion au service de paiement',
                'error' => $e->getMessage()
            ];
        }
    }

    public function transfer(array $data): array
    {
        try {
            $payload = [
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'reference' => $data['reference'] ?? 'TRANSFER_' . time(),
                'description' => $data['description'] ?? 'Money Transfer',
                'recipient' => [
                    'type' => $data['recipient']['type'],
                    'provider' => $data['recipient']['provider'],
                    'account_number' => $data['recipient']['account_number'],
                    'account_name' => $data['recipient']['account_name'],
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/transfers', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'reference' => $data['reference'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Erreur de transfert',
                'error' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('NotchPay Transfer Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de connexion au service de transfert',
                'error' => $e->getMessage()
            ];
        }
    }

    public function verifyTransaction(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/payments/' . $reference);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'status' => $data['status'] ?? 'pending',
                ];
            }

            return [
                'success' => false,
                'message' => 'Transaction non trouvée',
                'error' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('NotchPay Verify Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de vérification',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getSupportedMethods(string $country = 'CM'): array
    {
        $methods = config('payment.supported_countries.' . $country . '.methods', []);
        
        return [
            'success' => true,
            'data' => $methods
        ];
    }

    public function createPaymentLink(array $data): array
    {
        return $this->charge($data);
    }

    public function handleWebhook(array $payload): array
    {
        try {
            // Vérifier la signature du webhook
            $signature = request()->header('X-NotchPay-Signature');
            $webhookSecret = config('payment.notchpay.webhook_secret');
            
            if ($signature !== hash_hmac('sha256', json_encode($payload), $webhookSecret)) {
                return [
                    'success' => false,
                    'message' => 'Signature invalide'
                ];
            }

            return [
                'success' => true,
                'data' => $payload,
                'event' => $payload['event'] ?? 'unknown'
            ];

        } catch (\Exception $e) {
            Log::error('NotchPay Webhook Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur webhook',
                'error' => $e->getMessage()
            ];
        }
    }
}
