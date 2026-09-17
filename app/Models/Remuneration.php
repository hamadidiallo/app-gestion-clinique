<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Remuneration extends Model
{
    protected $fillable = [
        'medecin_id',
        'user_id',
        'type_remuneration',
        'periode_debut',
        'periode_fin',
        'salaire_fixe',
        'pourcentage',
        'montant_base',
        'montant_medecin',
        'montant_clinique',
        'statut',
        'date_paiement',
        'description',
    ];

    protected $casts = [
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'date_paiement' => 'date',
        'salaire_fixe' => 'decimal:2',
        'pourcentage' => 'decimal:2',
        'montant_base' => 'decimal:2',
        'montant_medecin' => 'decimal:2',
        'montant_clinique' => 'decimal:2',
    ];

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
