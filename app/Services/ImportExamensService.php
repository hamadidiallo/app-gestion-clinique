<?php

namespace App\Services;

use App\Models\Administration\Clinique;
use App\Models\Facturation\Service;
use App\Models\Medical\Acte;
use App\Models\Medical\ActeParametre;
use App\Models\Scopes\BelongsToCliniqueScope;
use Illuminate\Support\Facades\DB;

/**
 * Importe des examens de laboratoire et leurs paramètres dans une clinique,
 * depuis le catalogue de départ ou depuis un fichier CSV.
 *
 * Un examen est retrouvé par son code dans la clinique, un paramètre par son nom dans l'examen :
 * un nouvel import ne crée donc jamais de doublon.
 */
class ImportExamensService
{
    /** Colonnes du fichier CSV, dans l'ordre du modèle fourni. */
    public const COLONNES_CSV = [
        'code_examen', 'nom_examen', 'tarif', 'parametre', 'type', 'unite',
        'min', 'max', 'min_homme', 'max_homme', 'min_femme', 'max_femme',
        'min_enfant', 'max_enfant', 'valeur_normale_texte',
    ];

    private const CODE_SERVICE_LABORATOIRE = 'LAB-BIO';

    /**
     * Examens du catalogue de départ.
     *
     * @return list<array<string, mixed>>
     */
    public function catalogue(): array
    {
        return require database_path('data/catalogue_biologie.php');
    }

    /**
     * Importe une liste d'examens.
     *
     * @param  list<array<string, mixed>>  $examens
     * @param  bool  $remplacerParametres  vrai : les paramètres existants prennent les valeurs importées ;
     *                                     faux : seuls les paramètres manquants sont ajoutés (les réglages de la clinique sont gardés)
     * @return array{examens_crees: int, examens_completes: int, parametres_ajoutes: int, parametres_mis_a_jour: int}
     */
    public function importer(Clinique $clinique, array $examens, bool $remplacerParametres): array
    {
        $bilan = ['examens_crees' => 0, 'examens_completes' => 0, 'parametres_ajoutes' => 0, 'parametres_mis_a_jour' => 0];

        DB::transaction(function () use ($clinique, $examens, $remplacerParametres, &$bilan) {
            $service = $this->serviceLaboratoire($clinique);

            foreach ($examens as $examen) {
                $acte = Acte::withoutGlobalScope(BelongsToCliniqueScope::class)
                    ->where('clinique_id', $clinique->id)
                    ->where('code', $examen['code'])
                    ->first();

                $tarif = isset($examen['tarif_normal']) ? (float) $examen['tarif_normal'] : null;

                if (! $acte) {
                    $acte = Acte::withoutGlobalScope(BelongsToCliniqueScope::class)->create([
                        'clinique_id' => $clinique->id,
                        'service_id' => $service->id,
                        'code' => $examen['code'],
                        'nom' => $examen['nom'],
                        'categorie' => 'biologie',
                        'tarif_normal' => $tarif ?? 0,
                        'part_medecin_pourcentage' => 20,
                        'part_clinique_pourcentage' => 80,
                        // Sans tarif, l'examen reste inactif : il ne peut pas être facturé à 0 FCFA par erreur
                        'statut' => ($tarif ?? 0) > 0,
                        'description' => $examen['description'] ?? null,
                    ]);
                    $bilan['examens_crees']++;
                } else {
                    if ($remplacerParametres && $tarif !== null && $tarif > 0) {
                        $acte->update(['tarif_normal' => $tarif]);
                    }
                    $bilan['examens_completes']++;
                }

                $this->importerParametres($acte, $examen['parametres'] ?? [], $remplacerParametres, $bilan);
            }
        });

        return $bilan;
    }

    /**
     * Lit un fichier CSV (séparateur « ; » ou « , », UTF-8 ou export Excel) et le regroupe par examen.
     *
     * @return array{examens: list<array<string, mixed>>, erreurs: list<string>}
     */
    public function lireCsv(string $chemin): array
    {
        $contenu = (string) file_get_contents($chemin);
        $contenu = preg_replace('/^\xEF\xBB\xBF/', '', $contenu);
        if (! mb_check_encoding($contenu, 'UTF-8')) {
            // Export Excel Windows en Windows-1252
            $contenu = mb_convert_encoding($contenu, 'UTF-8', 'Windows-1252');
        }

        $lignes = preg_split('/\r\n|\r|\n/', trim($contenu));
        $separateur = substr_count($lignes[0] ?? '', ';') >= substr_count($lignes[0] ?? '', ',') ? ';' : ',';

        $entetes = array_map(fn ($entete) => mb_strtolower(trim($entete)), str_getcsv(array_shift($lignes) ?? '', $separateur, '"', ''));
        $manquantes = array_diff(['code_examen', 'nom_examen', 'parametre'], $entetes);
        if ($manquantes) {
            return ['examens' => [], 'erreurs' => ['Colonnes manquantes : '.implode(', ', $manquantes).'. Utilisez le modèle de fichier fourni.']];
        }

        $examens = [];
        $erreurs = [];

        foreach ($lignes as $index => $brute) {
            $numero = $index + 2;
            if (trim($brute) === '' || trim(str_replace($separateur, '', $brute)) === '') {
                continue;
            }

            $valeurs = str_getcsv($brute, $separateur, '"', '');
            $ligne = [];
            foreach ($entetes as $position => $entete) {
                $ligne[$entete] = trim((string) ($valeurs[$position] ?? ''));
            }

            $code = mb_strtoupper($ligne['code_examen']);
            if ($code === '') {
                $erreurs[] = "Ligne {$numero} : le code de l'examen est vide.";

                continue;
            }

            $examens[$code] ??= ['code' => $code, 'nom' => '', 'description' => null, 'tarif_normal' => null, 'parametres' => []];
            if ($ligne['nom_examen'] !== '') {
                $examens[$code]['nom'] = $ligne['nom_examen'];
            }
            if (($ligne['tarif'] ?? '') !== '') {
                $tarif = ActeParametre::lireNombre($ligne['tarif']);
                $tarif === null
                    ? $erreurs[] = "Ligne {$numero} : tarif « {$ligne['tarif']} » illisible."
                    : $examens[$code]['tarif_normal'] = $tarif;
            }

            if ($ligne['parametre'] === '') {
                continue;
            }

            [$parametre, $erreursLigne] = $this->lireParametre($ligne, $numero);
            $erreurs = array_merge($erreurs, $erreursLigne);
            if ($parametre) {
                $examens[$code]['parametres'][] = $parametre;
            }
        }

        foreach ($examens as $code => $examen) {
            if ($examen['nom'] === '') {
                $erreurs[] = "Examen {$code} : le nom de l'examen n'est indiqué sur aucune ligne.";
                unset($examens[$code]);
            }
        }

        return ['examens' => array_values($examens), 'erreurs' => $erreurs];
    }

    /**
     * Contenu du modèle CSV à remplir, avec deux exemples.
     */
    public function modeleCsv(): string
    {
        $lignes = [
            self::COLONNES_CSV,
            ['ACT-NFS-01', 'Numération Formule Sanguine (NFS)', '6000', 'Hémoglobine', 'numerique', 'g/dL', '12', '17', '13', '17', '12', '16', '11', '14', ''],
            ['ACT-NFS-01', '', '', 'Plaquettes', 'numerique', 'G/L', '150', '400', '', '', '', '', '', '', ''],
            ['ACT-GE-01', 'Goutte épaisse / TDR paludisme', '2500', 'TDR paludisme', 'texte', '', '', '', '', '', '', '', '', '', 'Négatif'],
        ];

        // BOM UTF-8 et « ; » : Excel ouvre le fichier correctement en français
        return "\xEF\xBB\xBF".implode("\r\n", array_map(fn ($ligne) => implode(';', $ligne), $lignes))."\r\n";
    }

    /**
     * @param  array<string, string>  $ligne
     * @return array{0: ?array<string, mixed>, 1: list<string>}
     */
    private function lireParametre(array $ligne, int $numero): array
    {
        $erreurs = [];
        $parametre = ['nom' => $ligne['parametre'], 'unite' => ($ligne['unite'] ?? '') ?: null];

        foreach (['min', 'max', 'min_homme', 'max_homme', 'min_femme', 'max_femme', 'min_enfant', 'max_enfant'] as $colonne) {
            $brut = $ligne[$colonne] ?? '';
            $champ = 'valeur_'.$colonne;
            if ($brut === '') {
                $parametre[$champ] = null;

                continue;
            }
            $nombre = ActeParametre::lireNombre($brut);
            if ($nombre === null) {
                $erreurs[] = "Ligne {$numero} : « {$brut} » n'est pas un nombre (colonne {$colonne}).";
            }
            $parametre[$champ] = $nombre;
        }

        $parametre['valeur_normale_texte'] = ($ligne['valeur_normale_texte'] ?? '') ?: null;

        $type = mb_strtolower($ligne['type'] ?? '');
        $parametre['type'] = match (true) {
            in_array($type, ['texte', 'text', 'qualitatif'], true) => ActeParametre::TYPE_TEXTE,
            in_array($type, ['numerique', 'numérique', 'chiffre', 'nombre'], true) => ActeParametre::TYPE_NUMERIQUE,
            // Type non précisé : déduit des colonnes remplies
            $parametre['valeur_normale_texte'] !== null => ActeParametre::TYPE_TEXTE,
            default => ActeParametre::TYPE_NUMERIQUE,
        };

        return [$erreurs ? null : $parametre, $erreurs];
    }

    /**
     * @param  list<array<string, mixed>>  $parametres
     * @param  array<string, int>  $bilan
     */
    private function importerParametres(Acte $acte, array $parametres, bool $remplacer, array &$bilan): void
    {
        $existants = ActeParametre::withoutGlobalScope(BelongsToCliniqueScope::class)
            ->where('acte_id', $acte->id)
            ->get()
            ->keyBy(fn (ActeParametre $parametre) => mb_strtolower(trim($parametre->nom)));
        $ordre = (int) $existants->max('ordre');

        foreach ($parametres as $donnees) {
            $champs = array_merge([
                'unite' => null, 'type' => ActeParametre::TYPE_NUMERIQUE,
                'valeur_min' => null, 'valeur_max' => null,
                'valeur_min_homme' => null, 'valeur_max_homme' => null,
                'valeur_min_femme' => null, 'valeur_max_femme' => null,
                'valeur_min_enfant' => null, 'valeur_max_enfant' => null,
                'valeur_normale_texte' => null,
            ], $donnees);

            $existant = $existants->get(mb_strtolower(trim($champs['nom'])));

            if ($existant) {
                if ($remplacer) {
                    $existant->update($champs);
                    $bilan['parametres_mis_a_jour']++;
                }

                continue;
            }

            ActeParametre::withoutGlobalScope(BelongsToCliniqueScope::class)->create($champs + [
                'clinique_id' => $acte->clinique_id,
                'acte_id' => $acte->id,
                'ordre' => ++$ordre,
            ]);
            $bilan['parametres_ajoutes']++;
        }
    }

    private function serviceLaboratoire(Clinique $clinique): Service
    {
        return Service::withoutGlobalScope(BelongsToCliniqueScope::class)->firstOrCreate(
            ['clinique_id' => $clinique->id, 'code' => self::CODE_SERVICE_LABORATOIRE],
            ['nom' => 'Laboratoire d\'Analyses Médicales', 'statut' => true]
        );
    }
}
