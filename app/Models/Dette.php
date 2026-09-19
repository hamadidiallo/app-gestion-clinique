<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dette extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'reference',
        'ticket_id',
        'patient_id',
        'user_id',
        'montant_initial',
        'montant_paye',
        'reste_a_payer',
        'statut',
        'date_creation',
        'date_reglement',
        'description',
    ];

    /**
     * Auto-génération de la référence unique de dette à la création
     */
    protected static function booted(): void
    {
        static::creating(function (Dette $dette) {
            if (empty($dette->reference)) {
                $maxId = (int) (static::max('id') ?? 0);
                $dette->reference = 'DET-'.str_pad((string) ($maxId + 1), 5, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $casts = [
        'montant_initial' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'reste_a_payer' => 'decimal:2',
        'date_creation' => 'date',
        'date_reglement' => 'date',
    ];

    // Relation BIDIRECTIONNELLE DETTE ----> TICKET
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relation BIDIRECTIONNELLE DETTE ----> PATIENT
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // Relation BIDIRECTIONNELLE DETTE ----> USER
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Utiliser la référence de dette (ex: DET-00001) dans les URLs.
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
