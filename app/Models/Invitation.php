<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinique_id',
        'role_id',
        'code',
        'prenom',
        'nom',
        'email',
        'cree_par_user_id',
        'utilise_le',
        'utilise_par_user_id',
    ];

    protected $casts = [
        'utilise_le' => 'datetime',
    ];

    public function clinique(): BelongsTo
    {
        return $this->belongsTo(Clinique::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par_user_id');
    }

    public function utilisePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilise_par_user_id');
    }

    public function estUtilise(): bool
    {
        return $this->utilise_le !== null;
    }
}
