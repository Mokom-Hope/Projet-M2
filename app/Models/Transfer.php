<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'recipient_email',
        'recipient_phone',
        'amount',
        'currency',
        'fees',
        'total_amount',
        'security_question',
        'security_answer_hash',
        'status',
        'transfer_code',
        'payment_method_id',
        'recipient_payment_method_id',
        'expires_at',
        'claimed_at',
        'failed_attempts',
        'max_attempts',
        'notes',
        'exchange_rate',
        'recipient_amount',
        'recipient_currency',
        'gateway_reference',
        'gateway_response'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'recipient_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'expires_at' => 'datetime',
        'claimed_at' => 'datetime',
        'failed_attempts' => 'integer',
        'max_attempts' => 'integer',
        'gateway_response' => 'array',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function recipientPaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'recipient_payment_method_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canAttemptClaim()
    {
        return $this->failed_attempts < $this->max_attempts && 
               !$this->isExpired() && 
               $this->status === 'sent';
    }

    public function getRecipientIdentifierAttribute()
    {
        return $this->recipient_email ?: $this->recipient_phone;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'sent' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'expired' => 'bg-gray-100 text-gray-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            $transfer->transfer_code = strtoupper(substr(md5(uniqid()), 0, 8));
            $transfer->max_attempts = 3;
            $transfer->expires_at = now()->addDays(7);
            
            // Si pas de montant destinataire spécifié, utiliser le montant original
            if (!$transfer->recipient_amount) {
                $transfer->recipient_amount = $transfer->amount;
                $transfer->recipient_currency = $transfer->currency;
                $transfer->exchange_rate = 1;
            }
        });
    }
}
