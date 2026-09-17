<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModePaiement extends Model
{
    protected $fillable = [
        'nom',
        'code',
        'description',
        'statut',
    ];

    protected $casts = [
        'statut' => 'boolean',
    ];

    // RELATION MODEPAIEMENT --> PAIEMENT
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    // RELATION MODEPAIEMENT --> RECETTE
    public function recettes(): HasMany
    {
        return $this->hasMany(Recette::class);
    }

    // RELATION PATIENT ---> DEPENSES
    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }
}
