<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prenom',
        'nom',
        'sexe',
        'telephone',
        'statut',
    ];
    // La relation Patient -> CarteAssurance
    public function cartesAssurance(): HasMany
    {
        return $this->hasMany(CarteAssurance::class);
    }
    // La relation  Patient --> PRESTATION
    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }
    // La relation  Patient --> Tickets
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
    // La relation  Patient --> Dettes
    public function dettes(): HasMany
    {
        return $this->hasMany(Dette::class);
    }
}
