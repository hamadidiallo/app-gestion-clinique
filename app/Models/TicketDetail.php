<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketDetail extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'ticket_id',
        'prestation_id',
        'designation',
        'type_item',
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

    /**
     * Récupère le libellé propre de la ligne (désignation libre ou prestation liée).
     */
    public function getLibelleAttribute(): string
    {
        if (! empty($this->designation)) {
            return $this->designation;
        }

        return $this->prestation?->service?->nom ?? $this->prestation?->description ?? 'Acte / Prestation Médicale';
    }

    /**
     * Libellé humain du type d'élément.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type_item) {
            'medicament' => 'Médicament / Pharmacie',
            'hospitalisation' => 'Hospitalisation / Séjour',
            default => 'Acte / Soin Médical',
        };
    }

    /**
     * Icône Bootstrap associée.
     */
    public function getIconAttribute(): string
    {
        return match ($this->type_item) {
            'medicament' => 'bi-capsule',
            'hospitalisation' => 'bi-hospital',
            default => 'bi-heart-pulse',
        };
    }

    /**
     * Classe badge Bootstrap.
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->type_item) {
            'medicament' => 'bg-emerald-subtle text-emerald-700 border-emerald-200',
            'hospitalisation' => 'bg-purple-subtle text-purple-700 border-purple-200',
            default => 'bg-blue-subtle text-blue-700 border-blue-200',
        };
    }
}
