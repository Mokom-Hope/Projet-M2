<?php

namespace App\Services;

use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Http\Request;

class SecurityService
{
    /**
     * Enregistrer un événement de connexion
     *
     * @param User $user
     * @param Request $request
     * @return void
     */
    public function logLogin(User $user, Request $request)
    {
        SecurityLog::create([
            'user_id' => $user->id,
            'event_type' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'country' => null, // Peut-être enrichi avec un service de géolocalisation
            'city' => null,
            'details' => [
                'device' => $request->header('User-Agent'),
            ],
            'risk_level' => 'low',
            'is_suspicious' => false,
        ]);
    }
}
