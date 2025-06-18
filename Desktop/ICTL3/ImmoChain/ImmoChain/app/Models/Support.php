<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'message', 'reponse', 'statut'];

    public function utilisateur()
    {
        return $this->belongsTo(User::class);
    }
}
