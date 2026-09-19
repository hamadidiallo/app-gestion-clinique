<?php

namespace App\Traits;

use App\Models\Clinique;
use App\Models\Scopes\BelongsToCliniqueScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClinique
{
    /**
     * Initialisation du trait sur le modèle Eloquent.
     */
    public static function bootBelongsToClinique(): void
    {
        static::addGlobalScope(new BelongsToCliniqueScope);

        static::creating(function (Model $model) {
            if (empty($model->clinique_id) && auth()->check() && ! empty(auth()->user()->clinique_id)) {
                $model->clinique_id = auth()->user()->clinique_id;
            }
        });
    }

    /**
     * Relation vers la clinique propriétaire de cet enregistrement.
     */
    public function clinique(): BelongsTo
    {
        return $this->belongsTo(Clinique::class);
    }
}
