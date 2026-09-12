<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarif extends Model
{
    protected $fillable = [
        'service_id',
        'tarif_normal',
        'tarif_amo',
        'tarif_specifique',
        'date_debut',
        'date_fin',
        'statut',
        'description',
    ];
    protected $casts = [
        'tarif_normal' => 'decimal:2',
        'tarif_amo' => 'decimal:2',
        'tarif_specifique' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'statut' => 'boolean',
    ];
    // Relation de bidirectionalité Service --> Tarif
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    // LA RELATION TARIF ---> PRESTATION
    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }
}
