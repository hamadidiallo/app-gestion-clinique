<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recette extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'ticket_id',
        'paiement_id',
        'user_id',
        'mode_paiement_id',
        'montant',
        'date_recette',
        'reference',
        'description',
        'statut',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_recette' => 'date',
        'statut' => 'boolean',
    ];

    // RELATION BIDIRECTIONNELLE RECETTE ---> TICKET
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // RELATION BIDIRECTIONNELLE RECETTE ---> PAIEMENT
    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

    // RELATION BIDIRECTIONNELLE RECETTE ---> USER
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
