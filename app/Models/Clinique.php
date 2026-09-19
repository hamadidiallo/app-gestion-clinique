<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Clinique extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type_etablissement',
        'slug',
        'code',
        'code_invitation',
        'email',
        'telephone',
        'adresse',
        'ville',
        'pays',
        'devise',
        'logo',
        'plan',
        'statut',
        'date_expiration',
        'prefixe_ticket',
        'prefixe_patient',
    ];

    protected function casts(): array
    {
        return [
            'date_expiration' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Clinique $clinique) {
            if (empty($clinique->slug)) {
                $clinique->slug = Str::slug($clinique->nom);
            }
            if (empty($clinique->code)) {
                $clinique->code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $clinique->nom), 0, 3));
            }
            if (empty($clinique->code_invitation)) {
                $prefix = strtoupper($clinique->code ?? substr(preg_replace('/[^A-Za-z0-9]/', '', $clinique->nom), 0, 3));
                $clinique->code_invitation = $prefix.'-'.rand(1000, 9999);
            }
        });
    }

    /**
     * Résolution de liaison de modèle par slug ou par ID.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->firstOrFail();
    }

    public function estActive(): bool
    {
        if ($this->statut !== 'actif') {
            return false;
        }

        if ($this->date_expiration && Carbon::now()->startOfDay()->gt($this->date_expiration)) {
            return false;
        }

        return true;
    }

    public function joursRestantsAbonnement(): ?int
    {
        if (! $this->date_expiration) {
            return null; // Illimité
        }

        $diff = Carbon::now()->startOfDay()->diffInDays($this->date_expiration, false);

        return (int) $diff;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function caisses(): HasMany
    {
        return $this->hasMany(Caisse::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function medecins(): HasMany
    {
        return $this->hasMany(Medecin::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function actes(): HasMany
    {
        return $this->hasMany(Acte::class);
    }

    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function dettes(): HasMany
    {
        return $this->hasMany(Dette::class);
    }

    public function recettes(): HasMany
    {
        return $this->hasMany(Recette::class);
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    public function assurances(): HasMany
    {
        return $this->hasMany(Assurance::class);
    }
}
