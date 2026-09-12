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
    // Relation Service->tarif
    public function tarifs(): HasMany
    {
        return $this->hasMany(Tarif::class);
    }
    // LA RELATION SERVICE ---> PRESTATION
    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }
    // LA RELATION SERVICE ---> REGLEPARTAGE
    public function reglesPartage()
    {
        return $this->hasMany(ReglePartage::class);
    }
}
