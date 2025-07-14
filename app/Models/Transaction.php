<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'transfer_id',
        'type',
        'amount',
        'currency',
        'description',
        'reference',
        'status',
        'payment_gateway',
        'gateway_reference',
        'gateway_response',
        'balance_after',
        'fees',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'fees' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function getTypeIconAttribute()
    {
        $icons = [
            'credit' => 'arrow-down-circle',
            'debit' => 'arrow-up-circle',
            'fee' => 'minus-circle',
            'refund' => 'rotate-ccw',
        ];

        return $icons[$this->type] ?? 'circle';
    }

    public function getTypeColorAttribute()
    {
        $colors = [
            'credit' => 'text-green-600',
            'debit' => 'text-red-600',
            'fee' => 'text-orange-600',
            'refund' => 'text-blue-600',
        ];

        return $colors[$this->type] ?? 'text-gray-600';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (!$transaction->reference) {
                $transaction->reference = 'TXN_' . strtoupper(uniqid());
            }
        });
    }
}
