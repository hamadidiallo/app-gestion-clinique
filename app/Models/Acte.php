<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Acte extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'code',
        'nom',
        'categorie',
        'tarif_normal',
        'tarif_amo',
        'tarif_specifique',
        'part_medecin_pourcentage',
        'part_clinique_pourcentage',
        'statut',
        'description',
    ];

    protected $casts = [
        'statut' => 'boolean',
        'tarif_normal' => 'decimal:2',
        'tarif_amo' => 'decimal:2',
        'tarif_specifique' => 'decimal:2',
        'part_medecin_pourcentage' => 'decimal:2',
        'part_clinique_pourcentage' => 'decimal:2',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('statut', true);
    }

    /**
     * Obtenir le libellé formaté de la catégorie
     */
    public function getCategorieLibelleAttribute(): string
    {
        return match ($this->categorie) {
            'consultation' => 'Consultation',
            'chirurgie' => 'Acte Chirurgical',
            'imagerie' => 'Imagerie / Échographie',
            'biologie' => 'Laboratoire & Biologie',
            'soins' => 'Soins Infirmiers & Urgences',
            'exploration' => 'Exploration Fonctionnelle',
            'maternite' => 'Maternité / Accouchement',
            default => ucfirst($this->categorie),
        };
    }
}
