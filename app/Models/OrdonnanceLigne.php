<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdonnanceLigne extends Model
{
    protected $table = 'ordonnance_lignes';

    protected $fillable = [
        'ordonnance_id',
        'medicament',
        'forme',
        'dosage',
        'posologie',
        'duree',
        'instructions',
    ];

    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class);
    }
}
