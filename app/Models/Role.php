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
}
