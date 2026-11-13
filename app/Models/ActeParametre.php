<?php

namespace App\Models;

use App\Traits\BelongsToClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Paramètre mesuré par un examen de laboratoire, avec ses valeurs normales.
 *
 * Les bornes générales s'appliquent par défaut. Les bornes « enfant », « homme » et
 * « femme », quand elles sont renseignées, les remplacent pour le patient concerné.
 */
class ActeParametre extends Model
{
    use BelongsToClinique;

    public const TYPE_NUMERIQUE = 'numerique';

    public const TYPE_TEXTE = 'texte';

    /** Bornes facultatives par profil de patient. */
    public const PROFILS = ['homme', 'femme', 'enfant'];

    protected $fillable = [
        'clinique_id',
        'acte_id',
        'nom',
        'unite',
        'type',
        'valeur_min',
        'valeur_max',
        'valeur_min_homme',
        'valeur_max_homme',
        'valeur_min_femme',
        'valeur_max_femme',
        'valeur_min_enfant',
        'valeur_max_enfant',
        'valeur_normale_texte',
        'ordre',
    ];

    protected $casts = [
        'valeur_min' => 'decimal:3',
        'valeur_max' => 'decimal:3',
        'valeur_min_homme' => 'decimal:3',
        'valeur_max_homme' => 'decimal:3',
        'valeur_min_femme' => 'decimal:3',
        'valeur_max_femme' => 'decimal:3',
        'valeur_min_enfant' => 'decimal:3',
        'valeur_max_enfant' => 'decimal:3',
        'ordre' => 'integer',
    ];

    public function acte(): BelongsTo
    {
        return $this->belongsTo(Acte::class);
    }

    /**
     * Bornes applicables à un patient : enfant (si l'âge est connu), puis sexe, puis générales.
     *
     * @return array{0: ?float, 1: ?float}
     */
    public function bornesPour(?Patient $patient = null): array
    {
        $profils = [];
        if ($patient?->estEnfant()) {
            $profils[] = 'enfant';
        }
        if ($patient?->sexe === 'M') {
            $profils[] = 'homme';
        } elseif ($patient?->sexe === 'F') {
            $profils[] = 'femme';
        }

        foreach ($profils as $profil) {
            if ($this->aDesBornes($profil)) {
                return $this->bornes($profil);
            }
        }

        return $this->bornes(null);
    }

    /**
     * Indique si une valeur saisie sort des valeurs normales applicables au patient.
     * Une valeur vide ou un paramètre sans référence n'est jamais signalé.
     */
    public function estHorsNorme(?string $valeur, ?Patient $patient = null): bool
    {
        $valeur = trim((string) $valeur);

        if ($valeur === '') {
            return false;
        }

        if ($this->type === self::TYPE_TEXTE) {
            return filled($this->valeur_normale_texte)
                && mb_strtolower($valeur) !== mb_strtolower(trim($this->valeur_normale_texte));
        }

        $nombre = self::lireNombre($valeur);
        if ($nombre === null) {
            return false;
        }

        [$min, $max] = $this->bornesPour($patient);

        return ($min !== null && $nombre < $min) || ($max !== null && $nombre > $max);
    }

    /**
     * Valeurs normales applicables à un patient, ex. « 12 – 16 g/dL » ou « Négatif ».
     */
    public function valeursNormalesPour(?Patient $patient = null): string
    {
        if ($this->type === self::TYPE_TEXTE) {
            return (string) $this->valeur_normale_texte;
        }

        [$min, $max] = $this->bornesPour($patient);

        return $this->formaterBornes($min, $max);
    }

    /**
     * Toutes les valeurs normales, ex. « 13 – 17 · F 12 – 16 · Enf. 11 – 14 g/dL ».
     */
    public function getValeursNormalesAttribute(): string
    {
        if ($this->type === self::TYPE_TEXTE) {
            return (string) $this->valeur_normale_texte;
        }

        $libelles = ['homme' => 'H', 'femme' => 'F', 'enfant' => 'Enf.'];
        $morceaux = [];

        [$min, $max] = $this->bornes(null);
        if ($min !== null || $max !== null) {
            $morceaux[] = $this->formaterBornes($min, $max, false);
        }
        foreach (self::PROFILS as $profil) {
            if ($this->aDesBornes($profil)) {
                [$min, $max] = $this->bornes($profil);
                $morceaux[] = $libelles[$profil].' '.$this->formaterBornes($min, $max, false);
            }
        }

        return $morceaux ? implode(' · ', $morceaux).($this->unite ? ' '.$this->unite : '') : '';
    }

    /**
     * Accepte la virgule décimale française (« 12,5 »).
     */
    public static function lireNombre(?string $valeur): ?float
    {
        $normalise = str_replace([' ', ','], ['', '.'], trim((string) $valeur));

        return $normalise !== '' && is_numeric($normalise) ? (float) $normalise : null;
    }

    private function aDesBornes(string $profil): bool
    {
        return $this->{'valeur_min_'.$profil} !== null || $this->{'valeur_max_'.$profil} !== null;
    }

    /**
     * @return array{0: ?float, 1: ?float}
     */
    private function bornes(?string $profil): array
    {
        $suffixe = $profil ? '_'.$profil : '';
        $min = $this->{'valeur_min'.$suffixe};
        $max = $this->{'valeur_max'.$suffixe};

        return [$min !== null ? (float) $min : null, $max !== null ? (float) $max : null];
    }

    private function formaterBornes(?float $min, ?float $max, bool $avecUnite = true): string
    {
        $format = fn (float $nombre) => rtrim(rtrim(number_format($nombre, 3, ',', ' '), '0'), ',');
        $unite = $avecUnite && $this->unite ? ' '.$this->unite : '';

        return match (true) {
            $min !== null && $max !== null => $format($min).' – '.$format($max).$unite,
            $min !== null => '≥ '.$format($min).$unite,
            $max !== null => '≤ '.$format($max).$unite,
            default => '',
        };
    }
}
