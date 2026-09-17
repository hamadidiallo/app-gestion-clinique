<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieDepense extends Model
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

    // RELATION CATEGORIEDEPENSE ---> DEPENSES
    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }
}
