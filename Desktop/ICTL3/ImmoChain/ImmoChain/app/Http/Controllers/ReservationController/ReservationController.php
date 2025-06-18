<?php

namespace App\Http\Controllers\ReservationController;

use App\Models\Bien;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    protected $blockchainService;
    
    public function __construct(BlockchainService $blockchainService)
    {
        $this->blockchainService = $blockchainService;
    }
    
    /**
     * Créer une nouvelle réservation
     */
    /*public function store(Request $request)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour réserver un bien.'
            ], 403);
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:biens,id',
            'visit_date' => 'required|date|after:today',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier si le bien est disponible
        $property = Bien::findOrFail($request->property_id);
        if ($property->statut !== 'Disponible') {
            return response()->json([
                'success' => false,
                'message' => 'Ce bien n\'est plus disponible.'
            ], 400);
        }

        // Créer la réservation
        $reservation = Reservation::create([
            'id_bien' => $request->property_id,
            'id_client' => Auth::id(),
            'date_reservation' => now(),
            'date_visite' => $request->visit_date,
            'message' => $request->message,
            'statut' => 'pending',
        ]);

        // Mettre à jour le statut du bien
        $property->update(['statut' => 'Réservé']);

        // Créer une notification pour le propriétaire
        Notification::create([
            'user_id' => $property->id_proprietaire,
            'message' => 'Nouvelle réservation pour votre bien "' . $property->titre . '"',
            'statut' => 'NonLu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Réservation effectuée avec succès',
            'reservation_id' => $reservation->id
        ]);
    }*/

    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour réserver un bien.'
            ], 403);
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:biens,id',
            'visit_date' => 'required|date|after:today',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier si le bien est disponible
        $property = Bien::findOrFail($request->property_id);
        if ($property->statut !== 'Disponible') {
            return response()->json([
                'success' => false,
                'message' => 'Ce bien n\'est plus disponible.'
            ], 400);
        }

        try {
            // Créer la réservation
            $reservation = Reservation::create([
                'id_bien' => $request->property_id,
                'id_client' => Auth::id(),
                'date_reservation' => now(),
                'date_visite' => $request->visit_date,
                'message' => $request->message,
                'statut' => 'pending',
                'blockchain_registered' => false,
                'blockchain_tx' => null,
            ]);

            // Mettre à jour le statut du bien
            $property->update(['statut' => 'Réservé']);

            // Créer une notification pour le propriétaire
            Notification::create([
                'user_id' => $property->id_proprietaire,
                'message' => 'Nouvelle réservation pour votre bien "' . $property->titre . '"',
                'statut' => 'NonLu',
            ]);
            
            // Enregistrer la réservation sur la blockchain
            try {
                $blockchainResult = $this->blockchainService->registerReservationAlternative($reservation);
                
                if (!$blockchainResult) {
                    Log::warning('La réservation a été créée mais n\'a pas pu être enregistrée sur la blockchain.');
                }
            } catch (\Exception $e) {
                Log::error('Erreur blockchain pour la réservation: ' . $e->getMessage());
                // Continuer même si l'enregistrement blockchain échoue
            }

            return response()->json([
                'success' => true,
                'message' => 'Réservation effectuée avec succès et certifiée sur la blockchain',
                'reservation_id' => $reservation->id,
                'blockchain_registered' => $reservation->blockchain_registered
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la réservation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la réservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accepter une réservation
     */
    /*public function accept($id)
    {
        $reservation = Reservation::with('bien')->findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire du bien
        if (Auth::id() !== $reservation->bien->id_proprietaire) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à accepter cette réservation.'
            ], 403);
        }

        // Mettre à jour le statut de la réservation
        $reservation->update(['statut' => 'accepted']);

        // Créer une notification pour le client
        Notification::create([
            'user_id' => $reservation->id_client,
            'message' => 'Votre réservation pour "' . $reservation->bien->titre . '" a été acceptée.',
            'statut' => 'NonLu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Réservation acceptée avec succès'
        ]);
    }*/

    public function accept($id)
    {
        $reservation = Reservation::with('bien')->findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire du bien
        if (Auth::id() !== $reservation->bien->id_proprietaire) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à accepter cette réservation.'
            ], 403);
        }

        // Mettre à jour le statut de la réservation
        $reservation->update(['statut' => 'accepted']);

        // Créer une notification pour le client
        Notification::create([
            'user_id' => $reservation->id_client,
            'message' => 'Votre réservation pour "' . $reservation->bien->titre . '" a été acceptée.',
            'statut' => 'NonLu',
        ]);
        
        // Mettre à jour l'enregistrement blockchain si nécessaire
        if ($reservation->blockchain_registered) {
            try {
                $this->blockchainService->registerReservationAlternative($reservation);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour blockchain de la réservation: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Réservation acceptée avec succès'
        ]);
    }

    /**
     * Refuser une réservation
     */
    public function reject($id)
    {
        $reservation = Reservation::with('bien')->findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire du bien
        if (Auth::id() !== $reservation->bien->id_proprietaire) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à refuser cette réservation.'
            ], 403);
        }

        // Mettre à jour le statut de la réservation
        $reservation->update(['statut' => 'rejected']);

        // Remettre le bien comme disponible
        $reservation->bien->update(['statut' => 'Disponible']);

        // Créer une notification pour le client
        Notification::create([
            'user_id' => $reservation->id_client,
            'message' => 'Votre réservation pour "' . $reservation->bien->titre . '" a été refusée.',
            'statut' => 'NonLu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Réservation refusée avec succès'
        ]);
    }

    /**
     * API pour récupérer les réservations d'un propriétaire
     */
    public function apiGetOwnerReservations()
    {
        $properties = Bien::where('id_proprietaire', Auth::id())->pluck('id');
        
        $reservations = Reservation::whereIn('id_bien', $properties)
            ->with(['bien', 'client:id,nom,email,telephone'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($reservation) {
                $reservation->bien->images = json_decode($reservation->bien->images);
                return $reservation;
            });

        return response()->json($reservations);
    }

    /**
     * API pour récupérer les réservations récentes d'un propriétaire
     */
    public function apiGetRecentReservations()
    {
        $properties = Bien::where('id_proprietaire', Auth::id())->pluck('id');
        
        $reservations = Reservation::whereIn('id_bien', $properties)
            ->with(['bien', 'client:id,nom,email,telephone'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($reservation) {
                $reservation->bien->images = json_decode($reservation->bien->images);
                return $reservation;
            });

        return response()->json($reservations);
    }

    /**
     * API pour récupérer les réservations d'un client
     */
    public function apiGetClientReservations()
    {
        $reservations = Reservation::where('id_client', Auth::id())
            ->with(['bien.proprietaire:id,nom,email,telephone'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($reservation) {
                $reservation->bien->images = json_decode($reservation->bien->images);
                return $reservation;
            });

        return response()->json($reservations);
    }
}
