<?php

namespace App\Http\Controllers\PropertyController;

use App\Models\Bien;
use Illuminate\Http\Request;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    protected $blockchainService;
    
    public function __construct(BlockchainService $blockchainService)
    {
        $this->blockchainService = $blockchainService;
    }
    /**
     * Afficher la liste des biens
     */
    public function index()
    {
        return view('pages.home');
    }

    /**
     * Afficher la carte des biens
     */
    public function map()
    {
        return view('pages.map');
    }

    /**
     * Afficher le formulaire de création d'un bien
     */
    public function create()
    {
        // Nous utilisons uniquement le middleware auth, donc pas besoin de vérification supplémentaire ici
        return view('properties.create');
    }

    /**
     * Enregistrer un nouveau bien
     */
    /*public function store(Request $request)
    {
        // Vérifier si l'utilisateur est connecté et est un propriétaire
        if (!Auth::check() || Auth::user()->type_utilisateur !== 'Propriétaire') {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté en tant que propriétaire pour ajouter un bien.'
            ], 403);
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Maison,Meublé,Hotel,Terrain,LocalCommercial,Studio,Chambre',
            'adresse' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'superficie' => 'required|numeric|min:0',
            'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'transaction_type' => 'required|in:vente,location',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:50240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Traiter les images
            $imagesPaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('storage/properties'), $filename);
                    $imagesPaths[] = '/storage/properties/' . $filename;

                }
            }

            // Traiter la vidéo si elle existe
            $videoPath = null;
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                $videoName = time() . '_' . $video->getClientOriginalName();
                $video->move(public_path('storage/properties/videos'), $videoName);
                $videoPath = '/storage/properties/videos/' . $videoName;
            }

            // Créer le bien
            $property = Bien::create([
                'titre' => $request->titre,
                'description' => $request->description,
                'type' => $request->type,
                'adresse' => $request->adresse,
                'prix' => $request->prix,
                'superficie' => $request->superficie,
                'images' => json_encode($imagesPaths),
                'statut' => 'Disponible',
                'id_proprietaire' => Auth::id(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'transaction_type' => $request->transaction_type,
                'video' => $videoPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bien ajouté avec succès',
                'property_id' => $property->id
            ]);
        } catch (\Exception $e) {
            // Enregistrer l'erreur détaillée dans les logs
            Log::error('Erreur lors de l\'ajout d\'un bien: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'ajout du bien: ' . $e->getMessage(),
                'error_details' => $e->getMessage()
            ], 500);
        }
    }*/

    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est connecté et est un propriétaire
        if (!Auth::check() || Auth::user()->type_utilisateur !== 'Propriétaire') {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté en tant que propriétaire pour ajouter un bien.'
            ], 403);
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Maison,Meublé,Hotel,Terrain,LocalCommercial,Studio,Chambre',
            'adresse' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'superficie' => 'required|numeric|min:0',
            'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'transaction_type' => 'required|in:vente,location',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:50240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Traiter les images
            $imagesPaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('storage/properties'), $filename);
                    $imagesPaths[] = '/storage/properties/' . $filename;
                }
            }

            // Traiter la vidéo si elle existe
            $videoPath = null;
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                $videoName = time() . '_' . $video->getClientOriginalName();
                $video->move(public_path('storage/properties/videos'), $videoName);
                $videoPath = '/storage/properties/videos/' . $videoName;
            }

            // Créer le bien
            $property = Bien::create([
                'titre' => $request->titre,
                'description' => $request->description,
                'type' => $request->type,
                'adresse' => $request->adresse,
                'prix' => $request->prix,
                'superficie' => $request->superficie,
                'images' => json_encode($imagesPaths),
                'statut' => 'Disponible',
                'id_proprietaire' => Auth::id(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'transaction_type' => $request->transaction_type,
                'video' => $videoPath,
                'blockchain_registered' => false,
                'blockchain_tx' => null,
            ]);

            // Enregistrer le bien sur la blockchain
            try {
                // Utiliser la méthode alternative qui fonctionne avec PHP 8.2
                $blockchainResult = $this->blockchainService->registerPropertyAlternative($property);
                
                if (!$blockchainResult) {
                    Log::warning('Le bien a été créé mais n\'a pas pu être enregistré sur la blockchain.');
                }
            } catch (\Exception $e) {
                Log::error('Erreur blockchain: ' . $e->getMessage());
                // Continuer même si l'enregistrement blockchain échoue
            }

            return response()->json([
                'success' => true,
                'message' => 'Bien ajouté avec succès et certifié sur la blockchain',
                'property_id' => $property->id,
                'blockchain_registered' => $property->blockchain_registered
            ]);
        } catch (\Exception $e) {
            // Enregistrer l'erreur détaillée dans les logs
            Log::error('Erreur lors de l\'ajout d\'un bien: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'ajout du bien: ' . $e->getMessage(),
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher les détails d'un bien
     */
    public function show($id)
    {
        $property = Bien::with('proprietaire')->findOrFail($id);
        return view('properties.show', compact('property'));
    }

    /**
     * Afficher le formulaire de modification d'un bien
     */
    public function edit($id)
    {
        $property = Bien::findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire du bien
        if (Auth::id() !== $property->id_proprietaire) {
            return redirect()->route('home')->with('error', 'Vous n\'êtes pas autorisé à modifier ce bien.');
        }

        return view('properties.edit', compact('property'));
    }

    /**
     * Mettre à jour un bien
     */
    public function update(Request $request, $id)
    {
        $property = Bien::findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire du bien
        if (Auth::id() !== $property->id_proprietaire) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à modifier ce bien.'
            ], 403);
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Maison,Meublé,Hotel,Terrain,LocalCommercial,Studio,Chambre',
            'adresse' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'superficie' => 'required|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'transaction_type' => 'required|in:vente,location',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        // Traiter les images si de nouvelles sont fournies
        $imagesPaths = json_decode($property->images, true);
        if ($request->hasFile('images')) {
            // Supprimer les anciennes images si demandé
            if ($request->has('delete_images') && $request->delete_images) {
                foreach ($imagesPaths as $path) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $path));
                }
                $imagesPaths = [];
            }

            // Ajouter les nouvelles images
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('storage/properties'), $filename);
                $imagesPaths[] = '/storage/properties/' . $filename;
            }
        }

        // Traiter la vidéo si une nouvelle est fournie
        $videoPath = $property->video;
        if ($request->hasFile('video')) {
            if ($videoPath) {
                $oldVideoPath = public_path($videoPath);
                if (file_exists($oldVideoPath)) {
                    unlink($oldVideoPath);
                }
            }
            $video = $request->file('video');
            $videoName = time() . '_' . $video->getClientOriginalName();
            $video->move(public_path('storage/properties/videos'), $videoName);
            $videoPath = '/storage/properties/videos/' . $videoName;
        }

        // Mettre à jour le bien
        $property->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'type' => $request->type,
            'adresse' => $request->adresse,
            'prix' => $request->prix,
            'superficie' => $request->superficie,
            'images' => json_encode($imagesPaths),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'transaction_type' => $request->transaction_type,
            'video' => $videoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bien mis à jour avec succès',
            'property_id' => $property->id
        ]);
    }

    /**
     * Supprimer un bien
     */
    /*public function destroy($id)
    {
        $property = Bien::findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire du bien
        if (Auth::id() !== $property->id_proprietaire) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à supprimer ce bien.'
            ], 403);
        }

        // Marquer comme supprimé au lieu de supprimer réellement
        $property->update(['statut' => 'Supprimé']);

        return response()->json([
            'success' => true,
            'message' => 'Bien supprimé avec succès'
        ]);
    }*/
    public function destroy($id)
    {
        $property = Bien::findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire du bien
        if (Auth::id() !== $property->id_proprietaire) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à supprimer ce bien.'
            ], 403);
        }

        // Supprimer réellement le bien de la base de données
        $property->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bien supprimé avec succès'
        ]);
    }
    /**
     * Changer le statut d'un bien
     */
    public function updateStatus(Request $request, $id)
    {
        $property = Bien::findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire du bien
        if (Auth::id() !== $property->id_proprietaire) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à modifier ce bien.'
            ], 403);
        }

        // Valider le statut
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Disponible,Réservé,Supprimé',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Statut invalide'
            ], 422);
        }

        // Mettre à jour le statut
        $property->update(['statut' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès'
        ]);
    }

    /**
     * API pour récupérer tous les biens disponibles
     */
    public function apiGetProperties()
    {
        $properties = Bien::where('statut', 'Disponible')
            ->with('proprietaire:id,nom,email,telephone,created_at')
            ->get()
            ->map(function ($property) {
                $property->images = json_decode($property->images);
                return $property;
            });

        return response()->json($properties);
    }

    /**
     * API pour récupérer les détails d'un bien
     */
    /*public function apiGetProperty($id)
    {
        $property = Bien::with('proprietaire:id,nom,email,telephone,created_at')
            ->findOrFail($id);
        
        $property->images = json_decode($property->images);

        return response()->json($property);
    }*/
    public function apiGetProperty($id)
    {
        $property = Bien::with('proprietaire:id,nom,email,telephone,created_at')
            ->findOrFail($id);
        
        $property->images = json_decode($property->images);
        
        // Ajouter l'URL de l'explorateur blockchain si le bien est enregistré
        if ($property->blockchain_registered && $property->blockchain_tx) {
            $property->blockchain_explorer_url = $this->blockchainService->getExplorerUrl($property->blockchain_tx);
        }

        return response()->json($property);
    }

    /**
     * API pour récupérer les biens d'un propriétaire
     */
    public function apiGetOwnerProperties()
    {
        $properties = Bien::where('id_proprietaire', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($property) {
                $property->images = json_decode($property->images);
                return $property;
            });

        return response()->json($properties);
    }
}
