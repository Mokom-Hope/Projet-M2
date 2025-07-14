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

    /**
     * Obtenir le drapeau du pays
     */
    public function getCountryFlag()
    {
        $flags = [
            'CM' => '🇨🇲',
            'CI' => '🇨🇮',
            'SN' => '🇸🇳',
            'BF' => '🇧🇫',
            'ML' => '🇲🇱',
            'NE' => '🇳🇪',
            'TD' => '🇹🇩',
            'GA' => '🇬🇦',
            'CG' => '🇨🇬',
            'CF' => '🇨🇫',
        ];
        return $flags[$this->country_code] ?? '🌍';
    }

    /**
     * Obtenir le nom du pays
     */
    public function getCountryName()
    {
        $countries = [
            'CM' => 'Cameroun',
            'CI' => 'Côte d\'Ivoire',
            'SN' => 'Sénégal',
            'BF' => 'Burkina Faso',
            'ML' => 'Mali',
            'NE' => 'Niger',
            'TD' => 'Tchad',
            'GA' => 'Gabon',
            'CG' => 'Congo',
            'CF' => 'République Centrafricaine',
        ];
        return $countries[$this->country_code] ?? 'Inconnu';
    }

    /**
     * Obtenir l'icône du type
     */
    public function getTypeIcon()
    {
        $icons = [
            'mobile_money' => '📱',
            'bank_account' => '🏦',
            'card' => '💳',
        ];
        return $icons[$this->type] ?? '💰';
    }

    /**
     * Obtenir le logo du fournisseur
     */
    public function getProviderLogo()
    {
        $logos = [
            'orange_money' => '🟠',
            'mtn_mobile_money' => '🟡',
            'moov_money' => '🔵',
            'wave' => '🌊',
            'ecobank' => '🟢',
            'uba' => '🔴',
            'sgbci' => '🟣',
            'bicici' => '⚫',
            'nsia_banque' => '🟤',
        ];
        $providerKey = strtolower(str_replace(' ', '_', $this->provider));
        return $logos[$providerKey] ?? '🏛️';
    }

    /**
     * Scope pour les méthodes actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les méthodes par défaut
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope pour les méthodes vérifiées
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par pays
     */
    public function scopeByCountry($query, $countryCode)
    {
        return $query->where('country_code', $countryCode);
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
