<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarteAssurance extends Model
{
    use BelongsToClinique;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinique_id',
        'patient_id',
        'assurance_id',
        'reference',
        'taux_couverture',
        'date_debut',
        'date_fin',
        'statut',
    ];

    /**
     * Les typages d'attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'taux_couverture' => 'decimal:2',
        'statut' => 'boolean',
    ];

    /**
     * Relation d'appartenance : Une carte d'assurance appartient à un patient.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation d'appartenance : Une carte d'assurance appartient à une compagnie d'assurance.
     */
    public function assurance(): BelongsTo
    {
        return $this->belongsTo(Assurance::class);
    }
}
