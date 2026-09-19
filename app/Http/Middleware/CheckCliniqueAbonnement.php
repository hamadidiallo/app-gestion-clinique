<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCliniqueAbonnement
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Les Super-Administrateurs de la plateforme SaaS ne sont jamais bloqués
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return $next($request);
            }

            $clinique = $user->clinique;

            // Si l'utilisateur a une clinique et qu'elle n'est pas active
            if ($clinique && ! $clinique->estActive()) {
                // Si la requête est déjà sur la page d'avertissement ou de déconnexion, laisser passer
                if ($request->routeIs('abonnement.suspendu', 'logout')) {
                    return $next($request);
                }

                return redirect()->route('abonnement.suspendu');
            }
        }

        return $next($request);
    }
}
