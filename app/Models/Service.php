<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
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

    // LA RELATION SERVICE ---> PRESTATION
    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }

    // LA RELATION SERVICE ---> REGLEPARTAGE
    public function reglesPartage(): HasMany
    {
        return $this->hasMany(ReglePartage::class);
    }

    // Relation Service -> Actes
    public function actes(): HasMany
    {
        return $this->hasMany(Acte::class);
    }
}
