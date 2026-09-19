<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalActivite extends Model
{
    use BelongsToClinique;

    protected $fillable = [
        'clinique_id',
        'user_id',
        'action',
        'module',
        'objet_type',
        'objet_id',
        'description',
        'anciennes_valeurs',
        'nouvelles_valeurs',
        'adresse_ip',
        'date_action',
    ];

    protected $casts = [
        'anciennes_valeurs' => 'array',
        'nouvelles_valeurs' => 'array',
        'date_action' => 'datetime',
    ];

    // RELATION BIDIRECTIONNELLE JOURNALACTIVITE ---> USER
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
