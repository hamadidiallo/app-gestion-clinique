<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caisse extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'reference',
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

    /**
     * Auto-génération de la référence unique de session à la création
     */
    protected static function booted(): void
    {
        static::creating(function (Caisse $caisse) {
            if (empty($caisse->reference)) {
                $maxId = (int) (static::max('id') ?? 0);
                $caisse->reference = 'SES-'.str_pad((string) ($maxId + 1), 5, '0', STR_PAD_LEFT);
            }
        });
    }

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

    /**
     * Utiliser la référence de session (ex: SES-00001) dans les URLs.
     */
    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    /**
     * Résolution de liaison de modèle par référence avec fallback sur l'ID.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('reference', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->firstOrFail();
    }
}
