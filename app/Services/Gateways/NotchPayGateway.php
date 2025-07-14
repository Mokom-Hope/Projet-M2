<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class NotchPayGateway
{
    private string $baseUrl;
    private string $publicKey;
    private string $privateKey;
    private string $environment;
    protected $client;

    public function __construct()
    {
        $this->baseUrl = 'https://api.notchpay.co/'; // Sans /v1
        $this->publicKey = config('payment.notchpay.public_key');
        $this->privateKey = config('payment.notchpay.private_key');
        $this->environment = config('payment.notchpay.environment', 'sandbox');
        
        // Configuration du client HTTP comme dans votre exemple
        $options = [
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => $this->publicKey, // Clé publique directement
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ];
        
        $this->client = new Client($options);
    }

    /**
     * Méthode utilitaire pour faire une requête API
     */
    protected function makeApiRequest($method, $endpoint, $options = [])
    {
        try {
            // Faire la requête à l'API
            $response = $this->client->request($method, $endpoint, $options);
            
            // Récupérer le corps de la réponse
            $body = (string) $response->getBody();
            
            // Logger la réponse
            Log::info("Réponse NotchPay ($endpoint): " . $body);
            
            // Décoder la réponse JSON
            $result = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Erreur de décodage JSON: " . json_last_error_msg() . ", Corps: " . $body);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error("Erreur API NotchPay ($endpoint): " . $e->getMessage());
            throw $e;
        }
    }

    public function charge(array $data): array
    {
        try {
            Log::info('NotchPay Charge Request', $data);

            // Format exact comme dans votre exemple qui fonctionne
            $payload = [
                'customer' => [
                    'email' => $data['customer']['email'],
                    'name' => $data['customer']['name']
                ],
                'amount' => (int) $data['amount'], // Entier obligatoire
                'currency' => $data['currency'],
                'reference' => $data['reference'],
                'callback_url' => route('payment.callback'),
                'return_url' => route('transfers.show', $data['transfer_id']),
                'cancel_url' => route('transfers.show', $data['transfer_id']),
                'description' => $data['description'],
                'metadata' => [
                    'transfer_id' => $data['transfer_id'],
                    'payment_method_id' => $data['payment_method_id'],
                    'payment_type' => 'transfer'
                ]
            ];

            Log::info('NotchPay Payload', $payload);

            // Utiliser la méthode makeApiRequest comme dans votre exemple
            $result = $this->makeApiRequest('POST', 'payments', [
                'json' => $payload
            ]);
            
            // Vérifier si la réponse contient l'URL d'autorisation
            if (!isset($result['authorization_url'])) {
                throw new \Exception('URL d\'autorisation manquante dans la réponse NotchPay: ' . json_encode($result));
            }

            return [
                'success' => true,
                'reference' => $result['reference'] ?? $data['reference'],
                'payment_url' => $result['authorization_url'],
                'transaction_id' => $result['transaction']['id'] ?? null,
                'data' => $result,
                'message' => 'Paiement initié avec succès',
                // Ajouter les données pour le paiement intégré
                'payment_data' => [
                    'public_key' => $this->publicKey,
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'reference' => $data['reference'],
                    'customer' => $data['customer'],
                    'callback_url' => route('payment.callback'),
                    'return_url' => route('transfers.show', $data['transfer_id'])
                ]
            ];

        } catch (\Exception $e) {
            Log::error('NotchPay Charge Exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur de connexion au service de paiement: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function verifyTransaction(string $reference): array
    {
        try {
            // Utiliser la même méthode que dans votre exemple
            $result = $this->makeApiRequest('GET', 'payments/' . $reference);
            
            // Vérifier la structure de la réponse comme dans votre exemple
            if (!isset($result['transaction']) || !isset($result['transaction']['status'])) {
                throw new \Exception('Structure de réponse NotchPay invalide lors de la vérification: ' . json_encode($result));
            }
            
            return [
                'success' => true,
                'status' => $result['transaction']['status'],
                'data' => $result,
                'verified' => $result['transaction']['status'] === 'completed'
            ];

        } catch (\Exception $e) {
            Log::error('NotchPay Verify Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors de la vérification',
                'data' => null
            ];
        }
    }

    public function getSupportedMethods(string $country = 'CM'): array
    {
        // Méthodes supportées par NotchPay au Cameroun
        $methods = [
            'CM' => [
                'mobile_money' => [
                    'mtn' => [
                        'name' => 'MTN Mobile Money',
                        'icon' => 'phone',
                        'fields' => ['phone']
                    ],
                    'orange' => [
                        'name' => 'Orange Money',
                        'icon' => 'phone',
                        'fields' => ['phone']
                    ]
                ]
            ]
        ];

        return $methods[$country] ?? [];
    }
}
