<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medecin extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'code',
        'nom',
        'prenom',
        'telephone',
        'specialite',
        'type_remuneration',
        'pourcentage',
        'salaire_fixe',
        'statut',
    ];

    protected $casts = [
        'pourcentage' => 'decimal:2',
        'salaire_fixe' => 'decimal:2',
        'statut' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($medecin) {
            if (empty($medecin->code)) {
                $nomClean = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $medecin->nom ?: 'MED'), 0, 4));
                $medecin->code = 'DR-'.$nomClean.'-'.strtoupper(substr(uniqid(), -4));
            }
        });
    }

    // LA RELATION MEDECIN ----> PRESTATION
    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }

    // LA RELATION MEDECIN ---> RENUMERATIONS
    public function remunerations(): HasMany
    {
        return $this->hasMany(Remuneration::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Utiliser le code praticien dans les URLs au lieu de l'ID numérique.
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
