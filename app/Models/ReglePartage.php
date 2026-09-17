<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReglePartage extends Model
{
    protected $table = 'regles_partage';

    protected $fillable = [
        'service_id',
        'pourcentage_medecin',
        'pourcentage_clinique',
        'date_debut',
        'date_fin',
        'statut',
        'description',
    ];

    protected $casts = [
        'pourcentage_medecin' => 'decimal:2',
        'pourcentage_clinique' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'statut' => 'boolean',
    ];

    // Relation bidirectionnelle REGLEPARTAGE ---> SERVICE
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
