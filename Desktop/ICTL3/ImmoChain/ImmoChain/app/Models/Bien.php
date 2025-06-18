<?php

namespace App\Models;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bien extends Model
{
    use HasFactory;
    protected $fillable = [
        'titre',
        'description',
        'type',
        'adresse',
        'prix',
        'superficie',
        'images',
        'statut',
        'id_proprietaire',
        'latitude',
        'longitude',
        'transaction_type',
        'video',
        'date_visite',

        //ajout des colones pour integrer la blockchain
        'blockchain_registered',
        'blockchain_tx'
    ];
    public function proprietaire()
    {
        return $this->belongsTo(User::class, 'id_proprietaire');
    }

    /**
     * Get the reservations for the property.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'id_bien');
    }


    //fonctions pour integrer la blockchain
    /**
     * Vérifie si le bien est enregistré sur la blockchain
     */
    public function isBlockchainRegistered()
    {
        return $this->blockchain_registered && !empty($this->blockchain_tx);
    }
    
    /**
     * Obtient l'URL de l'explorateur blockchain pour ce bien
     */
    public function getBlockchainExplorerUrl()
    {
        if ($this->isBlockchainRegistered()) {
            return env('BLOCKCHAIN_EXPLORER_URL', 'http://localhost:3000/explorer') . '/transaction/' . $this->blockchain_tx;
        }
        return null;
    } 







}
