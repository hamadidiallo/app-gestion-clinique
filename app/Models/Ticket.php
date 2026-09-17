<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    protected $fillable = [
        'patient_id',
        'assurance_id',
        'service_id',
        'medecin_id',
        'user_id',
        'reference',
        'date_ticket',
        'date_expiration',
        'montant_total',
        'montant_assurance',
        'montant_patient',
        'montant_paye',
        'reste_a_payer',
        'statut',
        'description',
    ];

    protected $casts = [
        'date_ticket' => 'datetime',
        'date_expiration' => 'datetime',
        'montant_total' => 'decimal:2',
        'montant_assurance' => 'decimal:2',
        'montant_patient' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'reste_a_payer' => 'decimal:2',
    ];

    // RELATION BIDIRECTIONNELLE TICKET ---> Patient
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // RELATION BIDIRECTIONNELLE TICKET ---> Assurance
    public function assurance(): BelongsTo
    {
        return $this->belongsTo(Assurance::class);
    }

    // RELATION BIDIRECTIONNELLE TICKET ---> Service
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // RELATION BIDIRECTIONNELLE TICKET ---> Medecin
    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    // RELATION BIDIRECTIONNELLE TICKET ---> User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // RELATION  TICKET ---> TicketDétails
    public function details(): HasMany
    {
        return $this->hasMany(TicketDetail::class);
    }

    // RELATION Ticket ---> Paiement
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    // RELATION TICKET ---> DETTE
    // Pourquoi hasOne ?
    // Parce qu'un ticket aura une dette correspondante, dont le montant évoluera au fur et à mesure des paiements.
    public function dette(): HasOne
    {
        return $this->hasOne(Dette::class);
    }

    // RELATION TICKET ---> RECETTE
    public function recettes(): HasMany
    {
        return $this->hasMany(Recette::class);
    }

    public function consultation(): HasOne
    {
        return $this->hasOne(Consultation::class);
    }

    public function actes(): HasMany
    {
        return $this->hasMany(TicketDetail::class)->where(function ($q) {
            $q->where('type_item', 'acte')->orWhereNull('type_item');
        });
    }

    public function medicaments(): HasMany
    {
        return $this->hasMany(TicketDetail::class)->where('type_item', 'medicament');
    }

    public function hospitalisations(): HasMany
    {
        return $this->hasMany(TicketDetail::class)->where('type_item', 'hospitalisation');
    }
}
