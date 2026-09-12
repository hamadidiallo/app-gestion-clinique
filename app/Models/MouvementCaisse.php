<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementCaisse extends Model
{
    protected $fillable = [
        'caisse_id',
        'user_id',
        'type',
        'origine',
        'reference',
        'montant',
        'date_mouvement',
        'description',
        'statut',
    ];
     protected $casts = [
        'montant' => 'decimal:2',
        'date_mouvement' => 'datetime',
        'statut' => 'boolean',
    ];
    // RELATION BIDIRECTIONNELLE MOUVEMENTCAISSE ---> CAISSE
    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }
    // RELATION BIDIRECTIONNELLE MOUVEMENTCAISSE ---> USER
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
