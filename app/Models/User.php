<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'country_code',
        'currency',
        'status',
        'kyc_status',
        'kyc_level',
        'two_factor_enabled',
        'profile_photo',
        'security_settings',
        'last_login_at',
        'last_login_ip'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'security_settings' => 'array',
        'last_login_at' => 'datetime',
        'failed_login_attempts' => 'integer',
        'locked_until' => 'datetime',
    ];

    // Relations
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function sentTransfers()
    {
        return $this->hasMany(Transfer::class, 'sender_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'recipient_id');
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function securityLogs()
    {
        return $this->hasMany(SecurityLog::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    // Accesseurs
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Méthodes
    public function isVerified()
    {
        return $this->email_verified_at && $this->phone_verified_at;
    }

    public function canSendMoney()
    {
        return $this->status === 'active' && 
               $this->isVerified() && 
               $this->kyc_status === 'approved' &&
               !$this->isLocked();
    }

    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementFailedAttempts()
    {
        $this->increment('failed_login_attempts');
        
        if ($this->failed_login_attempts >= 5) {
            $this->update(['locked_until' => now()->addMinutes(30)]);
        }
    }

    public function resetFailedAttempts()
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Créer automatiquement un portefeuille
            $user->wallet()->create([
                'currency' => $user->currency,
                'balance' => 0
            ]);
        });
    }
}
