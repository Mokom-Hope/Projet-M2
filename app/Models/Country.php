<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'currency',
        'phone_prefix',
        'is_active',
        'supported_payment_methods',
        'min_transfer_amount',
        'max_transfer_amount'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'supported_payment_methods' => 'array',
        'min_transfer_amount' => 'decimal:2',
        'max_transfer_amount' => 'decimal:2',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'country_code', 'code');
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'country_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedPhonePrefixAttribute()
    {
        return '+' . $this->phone_prefix;
    }

    public function supportsPaymentMethod($type, $provider)
    {
        $methods = $this->supported_payment_methods;
        
        if (!isset($methods[$type])) {
            return false;
        }

        return in_array($provider, $methods[$type]);
    }
}
