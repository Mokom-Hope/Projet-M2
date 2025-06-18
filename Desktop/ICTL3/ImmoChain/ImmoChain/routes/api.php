<?php

use App\Models\Bien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Immo\ImmoController;
use App\Http\Controllers\PropertyController\PropertyController;
use App\Http\Controllers\DashboardController\DashboardController;
use App\Http\Controllers\ReservationController\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes API publiques
Route::get('/properties', [PropertyController::class, 'apiGetProperties']);
Route::get('/properties/{id}', [PropertyController::class, 'apiGetProperty']);

// Important: Ajout d'une route non protégée pour l'ajout de biens
Route::post('/properties', [PropertyController::class, 'store']);

// Routes API protégées
Route::middleware(['auth:sanctum'])->group(function () {
    // Biens
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
    Route::put('/properties/{id}/status', [PropertyController::class, 'updateStatus']);
    
    // Réservations
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::post('/reservations/{id}/accept', [ReservationController::class, 'accept']);
    Route::post('/reservations/{id}/reject', [ReservationController::class, 'reject']);
    
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'apiGetStats']);
    Route::get('/dashboard/properties', [PropertyController::class, 'apiGetOwnerProperties']);
    Route::get('/dashboard/reservations', [ReservationController::class, 'apiGetOwnerReservations']);
    Route::get('/dashboard/reservations/recent', [ReservationController::class, 'apiGetRecentReservations']);
    Route::get('/dashboard/reservations/client', [ReservationController::class, 'apiGetClientReservations']);
});
// Route pour le chatbot immobilier
Route::post('/immo-chat', [App\Http\Controllers\Immo\ImmoController::class, 'processMessage']);

//route pour ajouter une ia
/*Route::post('/immo-chat', function (Request $request) {
    $message = trim($request->input('message'));
    // On récupère le nom de l'utilisateur, transmis depuis le front-end
    $name = $request->input('name', 'Client');

    $lowerMessage = strtolower($message);
    $reply = "";

    // Tentative d'extraction des critères (budget, type, transaction)
    $budget = null;
    if (preg_match('/budget(?:\s*[:=]?\s*)(\d+)/i', $message, $matchBudget)) {
        $budget = (float)$matchBudget[1];
    } elseif (preg_match('/(\d+)\s*(fcfa|f)/i', $message, $matchBudget)) {
        $budget = (float)$matchBudget[1];
    }

    // Détection du type de bien
    $typesMapping = [
        'maison'       => 'Maison',
        'terrain'      => 'Terrain',
        'local'        => 'LocalCommercial',
        'studio'       => 'Studio',
        'chambre'      => 'Chambre',
        'meublé'       => 'Meublé',
        'hotel'        => 'Hotel',
    ];
    $detectedType = null;
    foreach ($typesMapping as $keyword => $type) {
        if (strpos($lowerMessage, $keyword) !== false) {
            $detectedType = $type;
            break;
        }
    }

    // Détection de l'intention : location ou vente
    $transactionType = 'vente';
    if (strpos($lowerMessage, 'louer') !== false || strpos($lowerMessage, 'location') !== false) {
        $transactionType = 'location';
    }

    // Construire la requête sur la table des biens
    $query = Bien::where('statut', 'Disponible')
                ->where('transaction_type', $transactionType);
    if ($detectedType) {
        $query->where('type', $detectedType);
    }
    if ($budget) {
        // On accepte une marge de ±20 %
        $minPrice = $budget * 0.8;
        $maxPrice = $budget * 1.2;
        $query->whereBetween('prix', [$minPrice, $maxPrice]);
    }
    $biens = $query->take(3)->get();

    // Si aucun bien n'est trouvé, proposer d'ajuster les critères
    if ($biens->isEmpty()) {
        $reply = "Désolé {$name}, je n'ai trouvé aucun bien correspondant exactement à vos critères. Voulez-vous que je recherche d'autres options avec une légère variation de budget ou de type ?";
        return response()->json(['reply' => $reply]);
    }

    // Construction de la réponse détaillée et personnalisée
    $reply = "Très bien {$name}, j'ai analysé votre demande avec soin. Voici quelques biens que je vous recommande :<br><br>";
    foreach ($biens as $bien) {
        // Création de l'URL de détail vers la page de visite virtuelle/réservation
        // La fonction route() est ici utilisée pour générer le lien vers la page du bien
        $propertyUrl = route('properties.show', ['id' => $bien->id]);
        $reply .= "<strong>{$bien->titre}</strong> ({$bien->type})<br>";
        $reply .= "Prix : " . number_format($bien->prix, 0, ',', ' ') . " FCFA | Superficie : {$bien->superficie} m²<br>";
        $reply .= "Je vous conseille vivement de cliquer sur le lien ci-dessous pour visualiser le bien, faire une visite virtuelle et éventuellement réserver si cela correspond à vos attentes :<br>";
        $reply .= "<a href='{$propertyUrl}' target='_blank' class='text-indigo-600 underline'>Voir ce bien</a><br><br>";
    }
    $reply .= "Ces options présentent de légères variations par rapport à votre budget, mais elles offrent d'excellents atouts pour un investissement réussi. Souhaitez-vous affiner encore votre recherche ou souhaitez-vous d'autres conseils ?";
    return response()->json(['reply' => $reply]);
});*/
//route pour ajouter une ia