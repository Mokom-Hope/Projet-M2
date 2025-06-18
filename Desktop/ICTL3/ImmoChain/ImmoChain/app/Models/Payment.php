<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'property_id',
        'amount',
        'reference',
        'status',
        'payment_type',
        'metadata'
    ];

    /**
     * Obtenir l'utilisateur associé au paiement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir le bien associé au paiement.
     */
    public function property()
    {
        return $this->belongsTo(Bien::class, 'property_id');
    }

    /**
     * Obtenir la réservation associée au paiement.
     */
    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }
}

