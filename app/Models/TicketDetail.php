<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketDetail extends Model
{
    protected $fillable = [
        'ticket_id',
        'prestation_id',
        'quantite',
        'prix_unitaire',
        'montant_total',
        'taux_couverture',
        'montant_assurance',
        'montant_patient',
    ];
    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'taux_couverture' => 'decimal:2',
        'montant_assurance' => 'decimal:2',
        'montant_patient' => 'decimal:2',
    ];
    // RELATION BIDIRECTIONNELLE TICKETDETAIL ---> TICKET
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
    // RELATION BIDIRECTIONNELLE TICKETDETAIL ---> PRESTATION
    public function prestation(): BelongsTo
    {
        return $this->belongsTo(Prestation::class);
    }
}
