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
            // slug et code sont uniques en base. Or beaucoup d'établissements
            // partagent les mêmes premières lettres (« Clinique … », « Centre … ») :
            // sans dé-duplication, la deuxième inscription échouerait en base.
            if (empty($clinique->slug)) {
                $clinique->slug = static::valeurUnique('slug', Str::slug($clinique->nom) ?: 'clinique');
            }

            if (empty($clinique->code)) {
                $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $clinique->nom) ?? '', 0, 3)) ?: 'CLI';
                $clinique->code = static::valeurUnique('code', $base, 10);
            }

            if (empty($clinique->code_invitation)) {
                $clinique->code_invitation = static::genererCodeInvitation($clinique->code);
            }
        });
    }

    /**
     * Alphabet des codes dictés à l'oral : ni O/0 ni I/1, pour éviter les confusions.
     */
    private const ALPHABET_CODE = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Génère un code d'invitation lisible mais non devinable.
     *
     * Ce code suffit à rejoindre la clinique et à y obtenir un compte : il doit donc
     * résister à l'énumération. Un suffixe numérique à 4 chiffres n'offrait que
     * 9 000 possibilités, soit quelques heures de tentatives automatisées. Les six
     * caractères tirés ici portent l'espace à plus d'un milliard de combinaisons,
     * tout en restant dictables par téléphone.
     */
    public static function genererCodeInvitation(?string $prefixe = null): string
    {
        $prefixe = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $prefixe) ?: 'CLI');
        $prefixe = substr($prefixe, 0, 4);

        do {
            $suffixe = '';

            for ($i = 0; $i < 6; $i++) {
                $suffixe .= self::ALPHABET_CODE[random_int(0, strlen(self::ALPHABET_CODE) - 1)];
            }

            $code = $prefixe.'-'.$suffixe;
        } while (static::where('code_invitation', $code)->exists());

        return $code;
    }

    /**
     * Retourne une valeur libre pour une colonne unique, en suffixant si nécessaire.
     *
     * @param  string  $colonne  la colonne unique concernée
     * @param  string  $base  la valeur souhaitée
     * @param  int|null  $longueurMax  longueur maximale de la colonne, le cas échéant
     */
    protected static function valeurUnique(string $colonne, string $base, ?int $longueurMax = null): string
    {
        $tronquer = function (string $valeur) use ($longueurMax): string {
            return $longueurMax ? substr($valeur, 0, $longueurMax) : $valeur;
        };

        $candidat = $tronquer($base);
        $suffixe = 1;

        while (static::where($colonne, $candidat)->exists()) {
            $suffixe++;
            $marque = (string) $suffixe;

            // Le suffixe doit tenir dans la longueur maximale de la colonne
            $candidat = $longueurMax
                ? substr($base, 0, max(1, $longueurMax - strlen($marque))).$marque
                : $base.'-'.$marque;
        }

        return $candidat;
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
