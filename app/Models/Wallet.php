<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'status',
        'daily_limit',
        'monthly_limit',
        'daily_spent',
        'monthly_spent',
        'last_daily_reset',
        'last_monthly_reset'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'daily_limit' => 'decimal:2',
        'monthly_limit' => 'decimal:2',
        'daily_spent' => 'decimal:2',
        'monthly_spent' => 'decimal:2',
        'last_daily_reset' => 'date',
        'last_monthly_reset' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function canDebit($amount)
    {
        $this->resetLimitsIfNeeded();
        
        return $this->balance >= $amount && 
               $this->status === 'active' &&
               ($this->daily_spent + $amount) <= $this->daily_limit &&
               ($this->monthly_spent + $amount) <= $this->monthly_limit;
    }

    public function debit($amount, $description = null, $transferId = null)
    {
        if (!$this->canDebit($amount)) {
            throw new \Exception('Solde insuffisant ou limite dépassée');
        }

        $this->decrement('balance', $amount);
        $this->increment('daily_spent', $amount);
        $this->increment('monthly_spent', $amount);
        
        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'transfer_id' => $transferId,
            'type' => 'debit',
            'amount' => $amount,
            'currency' => $this->currency,
            'description' => $description ?? 'Débit portefeuille',
            'status' => 'completed',
            'balance_after' => $this->fresh()->balance
        ]);
    }

    public function credit($amount, $description = null, $transferId = null)
    {
        $this->increment('balance', $amount);
        
        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'transfer_id' => $transferId,
            'type' => 'credit',
            'amount' => $amount,
            'currency' => $this->currency,
            'description' => $description ?? 'Crédit portefeuille',
            'status' => 'completed',
            'balance_after' => $this->fresh()->balance
        ]);
    }

    public function getAvailableLimits()
    {
        $this->resetLimitsIfNeeded();
        
        return [
            'daily_available' => $this->daily_limit - $this->daily_spent,
            'monthly_available' => $this->monthly_limit - $this->monthly_spent,
        ];
    }

    private function resetLimitsIfNeeded()
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');

        if ($this->last_daily_reset !== $today) {
            $this->update([
                'daily_spent' => 0,
                'last_daily_reset' => $today
            ]);
        }

        if ($this->last_monthly_reset !== $thisMonth) {
            $this->update([
                'monthly_spent' => 0,
                'last_monthly_reset' => $thisMonth
            ]);
        }
    }
}
