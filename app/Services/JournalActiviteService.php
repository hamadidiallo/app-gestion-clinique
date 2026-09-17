<?php

namespace App\Services;

use App\Models\JournalActivite;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class JournalActiviteService
{
    /**
     * Enregistre une action sensible dans le journal d'activités.
     *
     * @param  string  $action  Nom court de l'action (ex: 'creation_ticket', 'paiement')
     * @param  string|null  $module  Nom du module métier (ex: 'facturation', 'caisse')
     * @param  string|null  $objetType  Nom de la classe ou table concernée
     * @param  int|null  $objetId  Identifiant de l'objet concerné
     * @param  string|null  $description  Explication lisible de l'action
     * @param  array|null  $anciennesValeurs  Données avant modification
     * @param  array|null  $nouvellesValeurs  Données après modification
     * @param  int|null  $userId  Identifiant de l'utilisateur (défaut: auth()->id())
     */
    public function log(
        string $action,
        ?string $module = null,
        ?string $objetType = null,
        ?int $objetId = null,
        ?string $description = null,
        ?array $anciennesValeurs = null,
        ?array $nouvellesValeurs = null,
        ?int $userId = null
    ): ?JournalActivite {
        $effectiveUserId = $userId ?? auth()->id();

        if (! $effectiveUserId) {
            $user = User::first();
            $effectiveUserId = $user ? $user->id : null;
        }

        if (! $effectiveUserId) {
            return null;
        }

        return JournalActivite::create([
            'user_id' => $effectiveUserId,
            'action' => $action,
            'module' => $module,
            'objet_type' => $objetType,
            'objet_id' => $objetId,
            'description' => $description,
            'anciennes_valeurs' => $anciennesValeurs,
            'nouvelles_valeurs' => $nouvellesValeurs,
            'adresse_ip' => Request::ip() ?? '127.0.0.1',
            'date_action' => now(),
        ]);
    }
}
