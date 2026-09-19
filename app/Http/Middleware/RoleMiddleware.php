<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // L'administrateur de clinique a accès à tous les modules métiers de sa clinique
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Vérifie si l'utilisateur possède l'un des rôles demandés
        if (! empty($roles) && $user->hasRole($roles)) {
            return $next($request);
        }

        // Message clair si l'accès est refusé
        $rolesList = implode(', ', $roles);
        $userRole = $user->role->nom ?? 'Aucun rôle';

        abort(403, "Accès restreint. Cet espace nécessite l'un des rôles suivants : [{$rolesList}]. Votre profil actuel est : {$userRole}.");
    }
}
