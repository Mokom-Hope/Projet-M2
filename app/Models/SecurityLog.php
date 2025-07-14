<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'details',
        'risk_level',
        'is_suspicious'
    ];

    protected $casts = [
        'details' => 'array',
        'is_suspicious' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_level', 'high');
    }

    public function getRiskBadgeAttribute()
    {
        $badges = [
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->risk_level] ?? 'bg-gray-100 text-gray-800';
    }
}
