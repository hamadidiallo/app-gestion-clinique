<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Les attributs autorisés lors de l'assignation en masse (Mass Assignment).
     *
     * @var list<string>
     */
    protected $fillable = [
        'clinique_id',
        'prenom',
        'nom',
        'email',
        'password',
        'role_id',
    ];

    /**
     * Les attributs masqués lors de la conversion en tableau/JSON.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtenir le typage des attributs (Casts).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Accesseur virtuel pour $user->name qui retourne le nom complet (Nom + Prénom).
     * Permet d'assurer la compatibilité avec toutes les vues Blade du système.
     */
    public function getNameAttribute(): string
    {
        return trim(($this->nom ?? '').' '.($this->prenom ?? ''));
    }

    /**
     * Relation vers la clinique de rattachement de l'utilisateur.
     */
    public function clinique(): BelongsTo
    {
        return $this->belongsTo(Clinique::class);
    }

    /**
     * Relation de bidirectionnalité : un utilisateur appartient à un rôle.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Vérifie si l'utilisateur possède l'un des rôles spécifiés.
     *
     * @param  string|array<string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        if (! $this->role) {
            return false;
        }

        $userRole = mb_strtolower(trim($this->role->nom));
        $normalizedUserRole = str_replace(['é', 'è', 'ê'], 'e', $userRole);

        $rolesList = is_array($roles) ? $roles : explode(',', $roles);

        foreach ($rolesList as $role) {
            $check = mb_strtolower(trim($role));
            $normalizedCheck = str_replace(['é', 'è', 'ê'], 'e', $check);

            if ($userRole === $check || $normalizedUserRole === $normalizedCheck) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['Administrateur', 'Admin']);
    }

    public function isMedecin(): bool
    {
        return $this->hasRole(['Médecin', 'Medecin']);
    }

    public function isCaissier(): bool
    {
        return $this->hasRole('Caissier');
    }

    public function isReceptionniste(): bool
    {
        return $this->hasRole(['Réceptionniste', 'Receptionniste']);
    }

    public function isComptable(): bool
    {
        return $this->hasRole('Comptable');
    }

    // Relation USER ---> Tickets
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // RELATION USER ---> Paiement
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    // RELATION USER ---> DETTES
    // Cela permet de connaître l'utilisateur qui a créé la dette.
    public function dettes(): HasMany
    {
        return $this->hasMany(Dette::class);
    }

    // RELATION USER ---> Recettes
    // On pourra donc savoir quel utilisateur a enregistré les recettes.
    public function recettes(): HasMany
    {
        return $this->hasMany(Recette::class);
    }

    // RELATION USER ---> CAISSE
    public function caisses(): HasMany
    {
        return $this->hasMany(Caisse::class);
    }

    // RELATION USER MOUVEMENT CAISSE
    public function mouvementsCaisses(): HasMany
    {
        return $this->hasMany(MouvementCaisse::class);
    }

    // RELATION USER ---> DEPENSES
    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    // RELATION USER ---> RENUMERATIONS
    public function remunerations(): HasMany
    {
        return $this->hasMany(Remuneration::class);
    }

    // RELATION USER ---> JOURNALACTIVITES
    public function journalActivites()
    {
        return $this->hasMany(JournalActivite::class);
    }
}
