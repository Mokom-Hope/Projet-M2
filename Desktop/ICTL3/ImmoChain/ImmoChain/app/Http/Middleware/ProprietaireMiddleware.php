<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\ProprietaireMiddleware as Middleware;

class ProprietaireMiddleware extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->type_utilisateur === 'Propriétaire') {
            return $next($request);
        }

        return redirect('/')->with('error', 'Vous devez être connecté en tant que propriétaire pour accéder à cette page.');
    }
}
