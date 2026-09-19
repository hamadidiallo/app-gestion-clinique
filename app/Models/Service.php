<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'nom',
        'code',
        'description',
        'statut',
    ];

    protected $casts = [
        'statut' => 'boolean',
    ];

    // LA RELATION SERVICE ---> PRESTATION
    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }

    // LA RELATION SERVICE ---> REGLEPARTAGE
    public function reglesPartage(): HasMany
    {
        return $this->hasMany(ReglePartage::class);
    }

    // Relation Service -> Actes
    public function actes(): HasMany
    {
        return $this->hasMany(Acte::class);
    }

    /**
     * Utiliser le code du service dans les URLs.
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * Résolution de liaison de modèle par code avec fallback sur l'ID.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('code', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->firstOrFail();
    }
}
