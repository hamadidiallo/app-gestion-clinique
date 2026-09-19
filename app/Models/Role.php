<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['nom', 'description'];

    // La methode qui determine la relation users -> roles
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Résolution de liaison de modèle par nom ou par ID.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('nom', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->firstOrFail();
    }
}
