<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BelongsToCliniqueScope implements Scope
{
    /**
     * Appliquer le scope à un Builder Eloquent donné.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $user = auth()->user();

            if (! empty($user->clinique_id)) {
                $builder->where($model->getTable().'.clinique_id', $user->clinique_id);
            }
        }
    }
}
