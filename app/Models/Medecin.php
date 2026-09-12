<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medecin extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'specialite',
        'type_remuneration',
        'pourcentage',
        'salaire_fixe',
        'statut',
    ];

    protected $casts = [
        'pourcentage' => 'decimal:2',
        'salaire_fixe' => 'decimal:2',
        'statut' => 'boolean',
    ];
    // LA RELATION MEDECIN ----> PRESTATION
    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }
    // LA RELATION MEDECIN ---> RENUMERATIONS
    public function remunerations(): HasMany
    {
        return $this->hasMany(Remuneration::class);
    }

}
