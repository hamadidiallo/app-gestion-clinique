<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ordonnance extends Model
{
    protected $fillable = [
        'consultation_id',
        'patient_id',
        'medecin_id',
        'reference',
        'date_ordonnance',
        'instructions_generales',
    ];

    protected $casts = [
        'date_ordonnance' => 'date',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(OrdonnanceLigne::class);
    }
}
