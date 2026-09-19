<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use BelongsToClinique;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinique_id',
        'reference',
        'prenom',
        'nom',
        'sexe',
        'telephone',
        'statut',
        'assurance_id',
        'numero_assure',
        'taux_couverture',
    ];

    /**
     * Auto-génération de la référence unique à la création
     */
    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            if (empty($patient->reference)) {
                $maxId = (int) (static::max('id') ?? 0);
                $patient->reference = 'PAT-'.str_pad((string) ($maxId + 1), 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Les typages d'attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'taux_couverture' => 'decimal:2',
    ];

    /**
     * Relation d'appartenance : Le patient appartient directement à un organisme d'assurance.
     */
    public function assurance(): BelongsTo
    {
        return $this->belongsTo(Assurance::class);
    }

    // La relation Patient -> CarteAssurance (rétrocompatibilité)
    public function cartesAssurance(): HasMany
    {
        return $this->hasMany(CarteAssurance::class);
    }

    // La relation  Patient --> PRESTATION
    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }

    // La relation  Patient --> Tickets
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // La relation  Patient --> Dettes
    public function dettes(): HasMany
    {
        return $this->hasMany(Dette::class);
    }

    /**
     * Le dossier médical pérenne du patient.
     */
    public function dossierMedical(): HasOne
    {
        return $this->hasOne(DossierMedical::class);
    }

    /**
     * Historique de toutes les consultations médicales du patient.
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Utiliser la référence patient (ex: PAT-00001) dans les URLs au lieu de l'ID.
     */
    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    /**
     * Résolution de liaison de modèle par référence avec fallback sur l'ID.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('reference', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->firstOrFail();
    }
}
