<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = ['id_reservation', 'montant', 'methode_paiement', 'date_paiement', 'statut_paiement','id_reservation'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
