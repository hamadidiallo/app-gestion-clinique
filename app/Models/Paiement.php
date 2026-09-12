<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paiement extends Model
{
    // Attributs modifiables en masse (mass assignable)
    protected $fillable = [
        'ticket_id',
        'user_id',
        'assurance_id',
        'reference',
        'type_payeur',
        'montant_recu',
        'montant_impute',
        'montant_rendu',
        'mode_paiement_id',
        'date_paiement',
        'statut',
        'description',
    ];

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
}
