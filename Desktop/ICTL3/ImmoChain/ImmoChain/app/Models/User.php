<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nom', 'email', 'password', 'telephone', 'type_utilisateur'];
    //protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the properties owned by the user.
     */
    public function biens()
    {
        return $this->hasMany(Bien::class, 'id_proprietaire');
    }

    /**
     * Get the reservations made by the user.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'id_client');
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}

