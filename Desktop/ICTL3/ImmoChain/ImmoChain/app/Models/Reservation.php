<?php

namespace App\Models;

use App\Models\Bien;
use App\Models\User;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_bien',
        'id_client',
        'date_reservation',
        'date_visite',
        'message',
        'statut',
        'montant_paye',
        'contact_proprietaire',

        //ajout des colones pour integrer la blockchain
        'blockchain_registered',
        'blockchain_tx'
    ];

    

    public function bien()
    {
        return $this->belongsTo(Bien::class, 'id_bien');
    }

    /**
     * Get the client who made the reservation.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'id_client');
    }

    //ajout de la fonction pour integrer la blockchain

    /**
     * Vérifie si la réservation est enregistrée sur la blockchain
     */
    public function isBlockchainRegistered()
    {
        return $this->blockchain_registered && !empty($this->blockchain_tx);
    }
    
    /**
     * Obtient l'URL de l'explorateur blockchain pour cette réservation
     */
    public function getBlockchainExplorerUrl()
    {
        if ($this->isBlockchainRegistered()) {
            return env('BLOCKCHAIN_EXPLORER_URL', 'http://localhost:3000/explorer') . '/transaction/' . $this->blockchain_tx;
        }
        return null;
    }






}
