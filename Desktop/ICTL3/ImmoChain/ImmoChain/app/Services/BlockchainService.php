<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class BlockchainService
{
    protected $apiUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->apiUrl = env('BLOCKCHAIN_API_URL', 'http://localhost:3000/api');
        $this->apiToken = env('BLOCKCHAIN_API_TOKEN', '');
    }

    /**
     * Authentification à l'API blockchain
     */
    protected function authenticate()
    {
        try {
            $response = Http::post($this->apiUrl . '/auth', [
                'username' => env('BLOCKCHAIN_API_USERNAME', 'admin'),
                'password' => env('BLOCKCHAIN_API_PASSWORD', 'password')
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->apiToken = $data['accessToken'];
                return true;
            } else {
                Log::error('Échec d\'authentification à l\'API blockchain: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception lors de l\'authentification à l\'API blockchain: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Méthode alternative pour enregistrer un bien immobilier sur la blockchain
     * Compatible avec PHP 8.2 et utilise des transactions de base de données
     */
    public function registerPropertyAlternative(Bien $property)
    {
        // Démarrer une transaction de base de données
        DB::beginTransaction();
        
        try {
            if (empty($this->apiToken)) {
                if (!$this->authenticate()) {
                    throw new \Exception("Échec d'authentification à la blockchain");
                }
            }

            // Préparer les données pour la blockchain
            $propertyData = [
                'id' => $property->id,
                'titre' => $property->titre,
                'description' => $property->description,
                'type' => $property->type,
                'adresse' => $property->adresse,
                'prix' => $property->prix,
                'superficie' => $property->superficie,
                'id_proprietaire' => $property->id_proprietaire,
                'created_at' => $property->created_at->toDateTimeString()
            ];
            
            // Appeler l'API blockchain
            $response = Http::withToken($this->apiToken)
                ->post($this->apiUrl . '/properties', $propertyData);
            
            if ($response->successful()) {
                $result = $response->json();
                
                // Mettre à jour le bien avec les informations de la transaction
                $property->blockchain_tx = $result['transaction']['id'];
                $property->blockchain_registered = true;
                $property->save();
                
                // Miner les transactions en attente
                if ($this->mineTransactions()) {
                    // Si tout s'est bien passé, valider la transaction de base de données
                    DB::commit();
                    Log::info('Bien immobilier enregistré sur la blockchain: ' . $result['transaction']['id']);
                    return true;
                } else {
                    throw new \Exception("Échec du minage des transactions");
                }
            } else {
                throw new \Exception("Erreur API blockchain: " . $response->body());
            }
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction de base de données
            DB::rollBack();
            Log::error('Exception lors de l\'enregistrement sur la blockchain: ' . $e->getMessage());
            
            // Réinitialiser les champs blockchain du bien
            $property->blockchain_tx = null;
            $property->blockchain_registered = false;
            $property->save();
            
            return false;
        }
    }
    
    /**
     * Méthode alternative pour enregistrer une réservation sur la blockchain
     * Compatible avec PHP 8.2 et utilise des transactions de base de données
     */
    public function registerReservationAlternative(Reservation $reservation)
    {
        // Démarrer une transaction de base de données
        DB::beginTransaction();
        
        try {
            if (empty($this->apiToken)) {
                if (!$this->authenticate()) {
                    throw new \Exception("Échec d'authentification à la blockchain");
                }
            }

            // Préparer les données pour la blockchain
            $reservationData = [
                'id' => $reservation->id,
                'id_bien' => $reservation->id_bien,
                'id_client' => $reservation->id_client,
                'date_reservation' => $reservation->date_reservation,
                'date_visite' => $reservation->date_visite,
                'statut' => $reservation->statut,
                'created_at' => $reservation->created_at->toDateTimeString()
            ];
            
            // Appeler l'API blockchain
            $response = Http::withToken($this->apiToken)
                ->post($this->apiUrl . '/reservations', $reservationData);
            
            if ($response->successful()) {
                $result = $response->json();
                
                // Mettre à jour la réservation avec les informations de la transaction
                $reservation->blockchain_tx = $result['transaction']['id'];
                $reservation->blockchain_registered = true;
                $reservation->save();
                
                // Miner les transactions en attente
                if ($this->mineTransactions()) {
                    // Si tout s'est bien passé, valider la transaction de base de données
                    DB::commit();
                    Log::info('Réservation enregistrée sur la blockchain: ' . $result['transaction']['id']);
                    return true;
                } else {
                    throw new \Exception("Échec du minage des transactions");
                }
            } else {
                throw new \Exception("Erreur API blockchain: " . $response->body());
            }
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction de base de données
            DB::rollBack();
            Log::error('Exception lors de l\'enregistrement de la réservation sur la blockchain: ' . $e->getMessage());
            
            // Réinitialiser les champs blockchain de la réservation
            $reservation->blockchain_tx = null;
            $reservation->blockchain_registered = false;
            $reservation->save();
            
            return false;
        }
    }

    /**
     * Miner les transactions en attente
     */
    protected function mineTransactions()
    {
        try {
            if (empty($this->apiToken)) {
                if (!$this->authenticate()) {
                    return false;
                }
            }

            $response = Http::withToken($this->apiToken)
                ->post($this->apiUrl . '/mine', [
                    'rewardAddress' => env('BLOCKCHAIN_REWARD_ADDRESS', 'system')
                ]);
            
            if ($response->successful()) {
                Log::info('Transactions minées avec succès');
                return true;
            } else {
                // Si l'erreur est "Aucune transaction en attente", c'est normal
                $error = $response->json();
                if (isset($error['message']) && $error['message'] === 'Aucune transaction en attente à miner') {
                    return true;
                }
                
                Log::error('Erreur lors du minage des transactions: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception lors du minage des transactions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir l'URL de l'explorateur de blockchain pour une transaction
     */
    public function getExplorerUrl($txId)
    {
        return env('BLOCKCHAIN_EXPLORER_URL', 'http://localhost:3000/explorer') . '/transaction/' . $txId;
    }
    
    /**
     * Vérifier si un bien est enregistré sur la blockchain
     */
    public function verifyProperty($propertyId)
    {
        try {
            $response = Http::get($this->apiUrl . '/properties/' . $propertyId);
            
            if ($response->successful()) {
                return $response->json();
            } else {
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception lors de la vérification du bien sur la blockchain: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Vérifier si une réservation est enregistrée sur la blockchain
     */
    public function verifyReservation($reservationId)
    {
        try {
            $response = Http::get($this->apiUrl . '/reservations/' . $reservationId);
            
            if ($response->successful()) {
                return $response->json();
            } else {
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception lors de la vérification de la réservation sur la blockchain: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enregistrer un bien immobilier sur la blockchain (méthode originale gardée pour compatibilité)
     */
    public function registerProperty(Bien $property)
    {
        return $this->registerPropertyAlternative($property);
    }
    
    /**
     * Enregistrer une réservation sur la blockchain (méthode originale gardée pour compatibilité)
     */
    public function registerReservation(Reservation $reservation)
    {
        return $this->registerReservationAlternative($reservation);
    }
}
