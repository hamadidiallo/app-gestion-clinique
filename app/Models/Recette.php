<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recette extends Model
{
    protected $fillable = [
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
    // RELATION BIDIRECTIONNELLE RECETTE ---> MODEPAIEMENT
    // public function modePaiement(): BelongsTo
    // {
    //     return $this->belongsTo(ModePaiement::class);
    // }

}
