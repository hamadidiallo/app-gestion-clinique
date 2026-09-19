<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Remuneration extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'reference',
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

    protected static function booted(): void
    {
        static::creating(function ($remuneration) {
            if (empty($remuneration->reference)) {
                $remuneration->reference = 'REM-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -5));
            }
        });
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Utiliser la référence métier dans les URLs au lieu de l'ID numérique.
     */
    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    /**
     * Résolution de liaison de modèle par référence avec fallback sur l'ID numérique.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('reference', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->firstOrFail();
    }
}
