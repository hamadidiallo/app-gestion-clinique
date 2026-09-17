<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    protected $fillable = [
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
}
