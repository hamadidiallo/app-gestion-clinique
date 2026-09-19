<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'categorie_depense_id',
        'mode_paiement_id',
        'user_id',
        'montant',
        'date_depense',
        'beneficiaire',
        'reference',
        'description',
        'statut',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_depense' => 'date',
        'statut' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($depense) {
            if (empty($depense->reference)) {
                $depense->reference = 'DEP-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -5));
            }
        });
    }

    // RELATION BIDIRECTIONNELLE CATEGORIEDEPENSE ---> DEPENSE
    public function categorieDepense(): BelongsTo
    {
        return $this->belongsTo(CategorieDepense::class);
    }

    // RELATION BIDIRECTIONNELLE MODEPAIEMENT ---> DEPENSE
    public function modePaiement(): BelongsTo
    {
        return $this->belongsTo(ModePaiement::class);
    }

    // RELATION BIDIRECTIONNELLE CATEGORIEDEPENSE ---> USER
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Utiliser la référence métier dans les URLs au lieu de l'ID numérique.
     */
    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    /**
     * Résolution de liaison de modèle par référence avec fallback sur l'ID numérique.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('reference', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->firstOrFail();
    }
}
