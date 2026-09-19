<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paiement extends Model
{
    use BelongsToClinique;

    // Attributs modifiables en masse (mass assignable)
    protected $fillable = [
        'clinique_id',
        'reference',
        'ticket_id',
        'user_id',
        'assurance_id',
        'type_payeur',
        'montant_recu',
        'montant_impute',
        'montant_rendu',
        'mode_paiement_id',
        'date_paiement',
        'statut',
        'description',
    ];

    /**
     * Auto-génération de la référence unique à la création
     */
    protected static function booted(): void
    {
        static::creating(function (Paiement $paiement) {
            if (empty($paiement->reference)) {
                $maxId = (int) (static::max('id') ?? 0);
                $paiement->reference = 'PAY-'.str_pad((string) ($maxId + 1), 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Conversion automatique des types de données (Casting)
    protected $casts = [
        'montant_recu' => 'decimal:2',
        'montant_impute' => 'decimal:2',
        'montant_rendu' => 'decimal:2',
        'date_paiement' => 'datetime',
    ];

    // Relation Bidirectionnelle Paiement ---> Ticket
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relation Bidirectionnelle Paiement ---> User (Caissier/Agent)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relation Bidirectionnelle Paiement ---> Assurance
    public function assurance(): BelongsTo
    {
        return $this->belongsTo(Assurance::class);
    }

    // Relation Bidirectionnelle Paiement ---> ModePaiement
    public function modePaiement(): BelongsTo
    {
        return $this->belongsTo(ModePaiement::class);
    }

    // RELATION PAIEMENT RECETTE
    // Un paiement validé pourra donc avoir sa recette correspondante.
    public function recette(): HasOne
    {
        return $this->hasOne(Recette::class);
    }

    /**
     * Utiliser la référence de paiement (ex: PAY-00001) dans les URLs.
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
