<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'provider',
        'account_number',
        'account_name',
        'country_code',
        'metadata',
        'is_verified',
        'is_default',
        'status'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public function sentTransfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'recipient_payment_method_id');
    }

    public function getDisplayNameAttribute()
    {
        return $this->provider . ' - ' . $this->getMaskedAccountNumber();
    }

    public function getMaskedAccountNumber()
    {
        $number = $this->account_number;
        if (strlen($number) > 4) {
            return '****' . substr($number, -4);
        }
        return $number;
    }

    public function getTypeIconAttribute()
    {
        $icons = [
            'mobile_money' => 'phone',
            'bank_account' => 'building-2',
            'card' => 'credit-card',
        ];

        return $icons[$this->type] ?? 'wallet';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paymentMethod) {
            // Si c'est le premier moyen de paiement, le marquer comme défaut
            if (!$paymentMethod->user->paymentMethods()->exists()) {
                $paymentMethod->is_default = true;
            }
        });

        static::saved(function ($paymentMethod) {
            // S'assurer qu'il n'y a qu'un seul moyen de paiement par défaut
            if ($paymentMethod->is_default) {
                $paymentMethod->user->paymentMethods()
                    ->where('id', '!=', $paymentMethod->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
