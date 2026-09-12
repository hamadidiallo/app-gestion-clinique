<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dette extends Model
{
    protected $fillable = [
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
} 
