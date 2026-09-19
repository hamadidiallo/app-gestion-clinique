<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consultation extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'patient_id',
        'medecin_id',
        'ticket_id',
        'user_id',
        'reference',
        'date_consultation',
        'tension_arterielle',
        'temperature',
        'poids',
        'taille',
        'pouls',
        'frequence_respiratoire',
        'glycemie',
        'saturation_oxygene',
        'motif_consultation',
        'histoire_maladie',
        'examen_physique',
        'diagnostic',
        'conduite_a_tenir',
        'statut',
    ];

    protected $casts = [
        'date_consultation' => 'datetime',
        'temperature' => 'decimal:1',
        'poids' => 'decimal:2',
        'glycemie' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ordonnance(): HasOne
    {
        return $this->hasOne(Ordonnance::class);
    }

    /**
     * Utiliser la référence de consultation dans les URLs au lieu de l'ID numérique.
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
