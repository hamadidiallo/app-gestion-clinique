<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    protected $fillable = [
        'prenom',
        'nom',
        'telephone',
        'email',
        'fonction',
        'type_remuneration',
        'salaire_fixe',
        'pourcentage',
        'date_embauche',
        'statut',
        'description',
    ];

    protected $casts = [
        'salaire_fixe' => 'decimal:2',
        'pourcentage' => 'decimal:2',
        'date_embauche' => 'date',
        'statut' => 'boolean',
    ];
}
