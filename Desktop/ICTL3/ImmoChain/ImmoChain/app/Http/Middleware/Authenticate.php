<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    // protected function redirectTo($request)
    // {
    //     // Si l'utilisateur n'est pas authentifié et qu'il ne s'attend pas à une réponse JSON
    //     if (!$request->expectsJson()) {
    //         // Si l'utilisateur est déjà connecté, redirigez vers le tableau de bord
    //         if ($request->user()) {
    //             return route('Dashboard');  // Assurez-vous que la route 'Dashboard' existe
    //         }
    //         // Sinon, redirigez vers la page d'inscription
    //         return route('login');  // Remplacez 'register' par le nom de la route d'inscription si nécessaire
    //     }
    // }
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
