<?php

namespace App\Http\Controllers\BlockchainController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\BlockchainService;
use App\Models\Bien;
use App\Models\Reservation;

class BlockchainController extends Controller
{
    protected $blockchainService;
    
    public function __construct(BlockchainService $blockchainService)
    {
        $this->blockchainService = $blockchainService;
    }
    
    /**
     * Afficher l'explorateur de blockchain
     */
    public function explorer()
    {
        try {
            $response = Http::get(env('BLOCKCHAIN_API_URL', 'http://localhost:3000/api') . '/blockchain');
            
            if ($response->successful()) {
                $blockchain = $response->json();
                return view('blockchain.explorer', compact('blockchain'));
            } else {
                return view('blockchain.explorer')->with('error', 'Impossible de récupérer les données de la blockchain');
            }
        } catch (\Exception $e) {
            return view('blockchain.explorer')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Afficher les détails d'un bloc
     */
    public function showBlock($index)
    {
        try {
            $response = Http::get(env('BLOCKCHAIN_API_URL', 'http://localhost:3000/api') . '/explorer/block/' . $index);
            
            if ($response->successful()) {
                $block = $response->json();
                return view('blockchain.block', compact('block'));
            } else {
                return redirect()->route('blockchain.explorer')->with('error', 'Bloc non trouvé');
            }
        } catch (\Exception $e) {
            return redirect()->route('blockchain.explorer')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Afficher les détails d'une transaction
     */
    public function showTransaction($id)
    {
        try {
            $response = Http::get(env('BLOCKCHAIN_API_URL', 'http://localhost:3000/api') . '/explorer/transaction/' . $id);
            
            if ($response->successful()) {
                $transaction = $response->json();
                return view('blockchain.transaction', compact('transaction'));
            } else {
                return redirect()->route('blockchain.explorer')->with('error', 'Transaction non trouvée');
            }
        } catch (\Exception $e) {
            return redirect()->route('blockchain.explorer')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Vérifier un bien immobilier sur la blockchain
     */
    public function verifyProperty($id)
    {
        $property = Bien::findOrFail($id);
        $blockchainData = $this->blockchainService->verifyProperty($id);
        
        if ($blockchainData) {
            return view('blockchain.verify-property', compact('property', 'blockchainData'));
        } else {
            return view('blockchain.verify-property', compact('property'))->with('error', 'Bien non trouvé sur la blockchain');
        }
    }
    
    /**
     * Vérifier une réservation sur la blockchain
     */
    public function verifyReservation($id)
    {
        $reservation = Reservation::with('bien', 'client')->findOrFail($id);
        $blockchainData = $this->blockchainService->verifyReservation($id);
        
        if ($blockchainData) {
            return view('blockchain.verify-reservation', compact('reservation', 'blockchainData'));
        } else {
            return view('blockchain.verify-reservation', compact('reservation'))->with('error', 'Réservation non trouvée sur la blockchain');
        }
    }
}
