<?php

namespace App\Http\Controllers\PaymentController;

use App\Models\Bien;
use App\Models\Reservation;
use App\Models\Notification;
use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use Psr\Http\Message\ResponseInterface;

class PaymentController extends Controller
{
    protected $client;
    
    public function __construct()
    {
        // Récupérer la clé publique depuis la configuration
        $publicKey = config('services.notchpay.public_key');
        
        // Configuration du client HTTP avec la nouvelle méthode d'authentification
        $options = [
            'base_uri' => 'https://api.notchpay.co/',
            'headers' => [
                'Authorization' => $publicKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ];
        
        $this->client = new Client($options);
    }
    
    /**
     * Méthode utilitaire pour faire une requête API et traiter la réponse
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

    /**
     * Initialiser un paiement pour accéder aux informations du propriétaire
     */
    public function initializePayment(Request $request)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer un paiement.'
            ], 403);
        }

        // Récupérer l'ID du bien
        $propertyId = $request->property_id;
        if (!$propertyId) {
            return response()->json([
                'success' => false,
                'message' => 'ID du bien manquant'
            ], 400);
        }

        // Récupérer le bien
        $property = Bien::with('proprietaire')->findOrFail($propertyId);

        try {
            // Générer une référence unique pour ce paiement
            $reference = 'IMMO-' . Auth::id() . '-' . uniqid();
            
            // Préparer les données pour NotchPay selon la nouvelle API
            $payload = [
                'customer' => [
                    'email' => Auth::user()->email,
                    'name' => Auth::user()->nom
                ],
                'amount' => 500,
                'currency' => 'XAF',
                'reference' => $reference,
                'callback_url' => route('notchpay.callback'),
                'return_url' => route('properties.show', $property->id),
                'cancel_url' => route('properties.show', $property->id),
                'description' => 'Accès aux informations du propriétaire pour le bien: ' . $property->titre,
                'metadata' => [
                    'property_id' => $propertyId,
                    'user_id' => Auth::id(),
                    'payment_type' => 'owner_info'
                ]
            ];

            // Journaliser la requête pour le débogage
            Log::info('Requête NotchPay (owner info): ' . json_encode($payload));

            // Faire la requête à l'API NotchPay avec la nouvelle URL
            $result = $this->makeApiRequest('POST', 'payments', [
                'json' => $payload
            ]);
            
            // Vérifier si la réponse contient l'URL d'autorisation
            if (!isset($result['authorization_url'])) {
                throw new \Exception('URL d\'autorisation manquante dans la réponse NotchPay: ' . json_encode($result));
            }

            // Enregistrer le paiement dans la base de données
            Payment::create([
                'user_id' => Auth::id(),
                'property_id' => $propertyId,
                'amount' => 500,
                'reference' => $reference,
                'status' => 'pending',
                'payment_type' => 'owner_info'
            ]);

            // Retourner l'URL de paiement
            return response()->json([
                'success' => true,
                'authorization_url' => $result['authorization_url'],
                'reference' => $reference
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'initialisation du paiement: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'initialisation du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initialiser un paiement pour une réservation
     */
    public function initializeReservationPayment(Request $request)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer une réservation.'
            ], 403);
        }

        // Valider les données
        $request->validate([
            'property_id' => 'required|exists:biens,id',
            'visit_date' => 'required|date|after:today',
            'message' => 'nullable|string',
        ]);

        // Récupérer le bien
        $property = Bien::findOrFail($request->property_id);

        // Vérifier si le bien est disponible
        if ($property->statut !== 'Disponible') {
            return response()->json([
                'success' => false,
                'message' => 'Ce bien n\'est plus disponible.'
            ], 400);
        }

        try {
            // Générer une référence unique pour ce paiement
            $reference = 'RESV-' . Auth::id() . '-' . uniqid();
            
            // Métadonnées pour la réservation
            $metadata = [
                'property_id' => $request->property_id,
                'user_id' => Auth::id(),
                'visit_date' => $request->visit_date,
                'message' => $request->message,
                'payment_type' => 'reservation'
            ];
            
            // Préparer les données pour NotchPay selon la nouvelle API
            $payload = [
                'customer' => [
                    'email' => Auth::user()->email,
                    'name' => Auth::user()->nom
                ],
                'amount' => 500,
                'currency' => 'XAF',
                'reference' => $reference,
                'callback_url' => route('notchpay.callback'),
                'return_url' => route('dashboard.reservations'),
                'cancel_url' => route('properties.show', $property->id),
                'description' => 'Réservation du bien: ' . $property->titre,
                'metadata' => $metadata
            ];

            // Journaliser la requête pour le débogage
            Log::info('Requête NotchPay (reservation): ' . json_encode($payload));

            // Faire la requête à l'API NotchPay avec la nouvelle URL
            $result = $this->makeApiRequest('POST', 'payments', [
                'json' => $payload
            ]);
            
            // Vérifier si la réponse contient l'URL d'autorisation
            if (!isset($result['authorization_url'])) {
                throw new \Exception('URL d\'autorisation manquante dans la réponse NotchPay: ' . json_encode($result));
            }

            // Enregistrer le paiement dans la base de données
            Payment::create([
                'user_id' => Auth::id(),
                'property_id' => $request->property_id,
                'amount' => 500,
                'reference' => $reference,
                'status' => 'pending',
                'payment_type' => 'reservation',
                'metadata' => json_encode($metadata)
            ]);

            // Retourner l'URL de paiement
            return response()->json([
                'success' => true,
                'authorization_url' => $result['authorization_url'],
                'reference' => $reference
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'initialisation du paiement de réservation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'initialisation du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Callback pour NotchPay
     */
    public function handleCallback(Request $request)
    {
        // Récupérer la référence du paiement
        $reference = $request->get('reference');
        if (!$reference) {
            Log::error('Callback NotchPay: Référence manquante');
            return redirect()->route('home')->with('error', 'Une erreur est survenue lors du traitement du paiement.');
        }

        try {
            // Récupérer le paiement dans notre base de données
            $payment = Payment::where('reference', $reference)->first();
            
            if (!$payment) {
                Log::error('Callback NotchPay: Paiement non trouvé pour la référence ' . $reference);
                return redirect()->route('home')->with('error', 'Paiement non trouvé.');
            }

            // Vérifier le statut du paiement auprès de NotchPay avec la nouvelle API
            $result = $this->makeApiRequest('GET', 'payments/' . $reference);
            
            // Vérifier la structure de la réponse
            if (!isset($result['transaction']) || !isset($result['transaction']['status'])) {
                throw new \Exception('Structure de réponse NotchPay invalide lors de la vérification: ' . json_encode($result));
            }
            
            // Mettre à jour le statut du paiement
            $paymentStatus = $result['transaction']['status'];
            if ($paymentStatus === 'completed') {
                $payment->status = 'completed';
                $payment->save();
                
                // Traiter selon le type de paiement
                if ($payment->payment_type === 'reservation') {
                    $this->processReservation($payment);
                    return redirect()->route('dashboard.reservations')->with('success', 'Votre réservation a été effectuée avec succès. Vous pouvez maintenant voir les informations du propriétaire.');
                } else {
                    // Paiement pour accéder aux informations du propriétaire
                    return redirect()->route('properties.show', $payment->property_id)->with('success', 'Paiement effectué avec succès. Vous pouvez maintenant voir les informations du propriétaire.');
                }
            } else if ($paymentStatus === 'canceled') {
                $payment->status = 'canceled';
                $payment->save();
                
                return redirect()->route('properties.show', $payment->property_id)->with('error', 'Le paiement a été annulé.');
            } else {
                $payment->status = 'failed';
                $payment->save();
                
                return redirect()->route('properties.show', $payment->property_id)->with('error', 'Le paiement a échoué. Veuillez réessayer.');
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du callback NotchPay: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Une erreur est survenue lors du traitement du paiement.');
        }
    }

    /**
     * Traiter une réservation après paiement réussi
     */
    private function processReservation($payment)
    {
        try {
            // Récupérer les métadonnées
            $metadata = json_decode($payment->metadata, true);
            
            // Créer la réservation
            $reservation = Reservation::create([
                'id_bien' => $payment->property_id,
                'id_client' => $payment->user_id,
                'date_reservation' => now(),
                'date_visite' => $metadata['visit_date'] ?? now()->addDays(1),
                'message' => $metadata['message'] ?? null,
                'statut' => 'pending',
                'payment_id' => $payment->id
            ]);

            // Récupérer le bien
            $property = Bien::findOrFail($payment->property_id);
            
            // Mettre à jour le statut du bien
            $property->update(['statut' => 'Réservé']);

            // Créer une notification pour le propriétaire
            Notification::create([
                'user_id' => $property->id_proprietaire,
                'message' => 'Nouvelle réservation pour votre bien "' . $property->titre . '"',
                'statut' => 'NonLu',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement de la réservation après paiement: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si l'utilisateur a payé pour voir les informations du propriétaire
     */
    public function checkOwnerInfoAccess(Request $request)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'has_access' => false,
                'message' => 'Vous devez être connecté pour accéder à ces informations.'
            ], 403);
        }

        $propertyId = $request->property_id;
        
        // Vérifier si l'utilisateur a une réservation acceptée pour ce bien
        $hasReservation = Reservation::where('id_bien', $propertyId)
            ->where('id_client', Auth::id())
            ->where('statut', 'accepted')
            ->exists();
            
        if ($hasReservation) {
            return response()->json([
                'success' => true,
                'has_access' => true,
                'message' => 'Vous avez accès aux informations du propriétaire.'
            ]);
        }
        
        // Vérifier si l'utilisateur a payé pour voir les informations du propriétaire
        $hasPaid = Payment::where('property_id', $propertyId)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->where(function($query) {
                $query->where('payment_type', 'owner_info')
                      ->orWhere('payment_type', 'reservation');
            })
            ->exists();
            
        return response()->json([
            'success' => true,
            'has_access' => $hasPaid,
            'message' => $hasPaid 
                ? 'Vous avez accès aux informations du propriétaire.' 
                : 'Vous devez payer pour accéder aux informations du propriétaire.'
        ]);
    }

    /**
     * Récupérer les informations du propriétaire après paiement
     */
    public function getOwnerInfo(Request $request)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour accéder à ces informations.'
            ], 403);
        }

        $propertyId = $request->property_id;
        
        // Vérifier si l'utilisateur a une réservation acceptée pour ce bien
        $hasReservation = Reservation::where('id_bien', $propertyId)
            ->where('id_client', Auth::id())
            ->where('statut', 'accepted')
            ->exists();
            
        // Vérifier si l'utilisateur a payé pour voir les informations du propriétaire
        $hasPaid = Payment::where('property_id', $propertyId)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->where(function($query) {
                $query->where('payment_type', 'owner_info')
                      ->orWhere('payment_type', 'reservation');
            })
            ->exists();
            
        if (!$hasReservation && !$hasPaid) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez payer pour accéder aux informations du propriétaire.'
            ], 403);
        }
        
        // Récupérer les informations du propriétaire
        $property = Bien::with('proprietaire')->findOrFail($propertyId);
        
        return response()->json([
            'success' => true,
            'owner' => [
                'name' => $property->proprietaire->nom,
                'email' => $property->proprietaire->email,
                'phone' => $property->proprietaire->telephone,
                'since' => $property->proprietaire->created_at
            ]
        ]);
    }

    /**
     * Méthode pour compléter un paiement mobile (MTN, Orange)
     * Cette méthode est nécessaire pour la deuxième étape du processus de paiement Direct
     */
    public function completeMobilePayment(Request $request)
    {
        // Valider les données
        $request->validate([
            'reference' => 'required|string',
            'phone' => 'required|string',
            'channel' => 'required|string|in:cm.mtn,cm.orange,cm.mobile'
        ]);

        try {
            // Récupérer le paiement
            $payment = Payment::where('reference', $request->reference)->first();
            
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paiement non trouvé.'
                ], 404);
            }

            // Préparer les données pour NotchPay
            $payload = [
                'channel' => $request->channel,
                'data' => [
                    'phone' => $request->phone
                ]
            ];

            // Faire la requête à l'API NotchPay
            $result = $this->makeApiRequest('PUT', 'payments/' . $request->reference, [
                'json' => $payload
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Veuillez confirmer le paiement sur votre téléphone.',
                'action' => $result['action'] ?? 'confirm'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la complétion du paiement mobile: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la complétion du paiement: ' . $e->getMessage()
            ], 500);
        }
    }
}
