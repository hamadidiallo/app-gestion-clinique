<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
}
