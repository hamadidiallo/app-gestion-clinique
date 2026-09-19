<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assurance extends Model
{
    use BelongsToClinique;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinique_id',
        'nom',
        'code',
        'taux_par_defaut',
        'telephone',
        'email',
        'adresse',
        'statut',
    ];

    /**
     * Les typages d'attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'taux_par_defaut' => 'decimal:2',
        'statut' => 'boolean',
    ];

    /**
     * Relation un-à-plusieurs : Une assurance couvre plusieurs patients.
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Relation un-à-plusieurs : Une assurance possède plusieurs cartes d'assurance.
     */
    public function cartesAssurance(): HasMany
    {
        return $this->hasMany(CarteAssurance::class);
    }

    // RELATION ASSURANCE ---> PAIEMENT
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    // RELATION ASSURANCE ---> TICKETS
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Utiliser le code assurance dans les URLs au lieu de l'ID numérique.
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * Résolution de liaison de modèle par code avec fallback sur l'ID numérique.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('code', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->firstOrFail();
    }
}
