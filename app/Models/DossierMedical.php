<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DossierMedical extends Model
{
    protected $table = 'dossiers_medicaux';

    protected $fillable = [
        'patient_id',
        'numero_dossier',
        'groupe_sanguin',
        'allergies',
        'antecedents_personnels',
        'antecedents_familiaux',
        'antecedents_chirurgicaux',
        'notes_particulieres',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
