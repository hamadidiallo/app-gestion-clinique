<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consultation extends Model
{
    protected $fillable = [
        'patient_id',
        'medecin_id',
        'ticket_id',
        'user_id',
        'reference',
        'date_consultation',
        'tension_arterielle',
        'temperature',
        'poids',
        'taille',
        'pouls',
        'frequence_respiratoire',
        'glycemie',
        'saturation_oxygene',
        'motif_consultation',
        'histoire_maladie',
        'examen_physique',
        'diagnostic',
        'conduite_a_tenir',
        'statut',
    ];

    protected $casts = [
        'date_consultation' => 'datetime',
        'temperature' => 'decimal:1',
        'poids' => 'decimal:2',
        'glycemie' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ordonnance(): HasOne
    {
        return $this->hasOne(Ordonnance::class);
    }
}
