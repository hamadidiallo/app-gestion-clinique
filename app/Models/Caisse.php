<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caisse extends Model
{
    protected $fillable = [
        'user_id',
        'date_ouverture',
        'date_fermeture',
        'fonds_initial',
        'total_entrees',
        'total_sorties',
        'solde_theorique',
        'solde_physique',
        'ecart',
        'statut',
        'observation',
    ];

    protected $casts = [
        'date_ouverture' => 'datetime',
        'date_fermeture' => 'datetime',
        'fonds_initial' => 'decimal:2',
        'total_entrees' => 'decimal:2',
        'total_sorties' => 'decimal:2',
        'solde_theorique' => 'decimal:2',
        'solde_physique' => 'decimal:2',
        'ecart' => 'decimal:2',
    ];

    // RELATION BIDIRECTIONNELLE CAISSE ---> USER
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // RELATION CAISSE ---> MOUVEMENTS
    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementCaisse::class);
    }
}
