<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalActivite extends Model
{
    protected $fillable = [
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
