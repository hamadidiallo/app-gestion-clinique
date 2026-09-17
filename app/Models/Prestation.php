<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prestation extends Model
{
    protected $fillable = [
        'patient_id',
        'service_id',
        'medecin_id',
        'acte_id',
        'type',
        'montant',
        'taux_couverture',
        'montant_assurance',
        'montant_patient',
        'pourcentage_medecin',
        'pourcentage_clinique',
        'part_medecin',
        'part_clinique',
        'date_prestation',
        'description',
        'statut',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'taux_couverture' => 'decimal:2',
        'montant_assurance' => 'decimal:2',
        'montant_patient' => 'decimal:2',
        'pourcentage_medecin' => 'decimal:2',
        'pourcentage_clinique' => 'decimal:2',
        'part_medecin' => 'decimal:2',
        'part_clinique' => 'decimal:2',
        'date_prestation' => 'datetime',
        'statut' => 'boolean',
    ];

    // RELATION BIDIRECTIONNELLE PATIENT ---> PRESTATION
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // RELATION BIDIRECTIONNELLE SERVICE ---> PRESTATION
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // RELATION BIDIRECTIONNELLE MEDECIN ---> PRESTATION
    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    // RELATION PRESTATION ---> ACTE MEDICAL
    public function acte(): BelongsTo
    {
        return $this->belongsTo(Acte::class);
    }

    // RELATION PRESTATION ----> TICKETDETAILS
    public function ticketDetails(): HasMany
    {
        return $this->hasMany(TicketDetail::class);
    }
}
