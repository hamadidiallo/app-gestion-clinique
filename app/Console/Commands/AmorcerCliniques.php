<?php

namespace App\Console\Commands;

use App\Models\Clinique;
use App\Services\AmorcageCliniqueService;
use Illuminate\Console\Command;

/**
 * Amorce le référentiel des cliniques déjà enregistrées.
 *
 * Les nouvelles cliniques sont dotées automatiquement à l'inscription ; cette
 * commande sert aux établissements créés avant la mise en place de l'amorçage,
 * typiquement lors du déploiement.
 */
class AmorcerCliniques extends Command
{
    /**
     * @var string
     */
    protected $signature = 'clinique:amorcer
                            {--id=* : Identifiants des cliniques à traiter (toutes par défaut)}';

    /**
     * @var string
     */
    protected $description = 'Crée le référentiel de démarrage (services, actes, assurances, catégories de dépenses) des cliniques existantes';

    public function handle(AmorcageCliniqueService $amorcage): int
    {
        $requete = Clinique::query();

        if ($ids = $this->option('id')) {
            $requete->whereIn('id', $ids);
        }

        $cliniques = $requete->orderBy('id')->get();

        if ($cliniques->isEmpty()) {
            $this->warn('Aucune clinique à traiter.');

            return self::SUCCESS;
        }

        foreach ($cliniques as $clinique) {
            // L'amorçage est idempotent : il complète sans écraser les personnalisations
            $amorcage->amorcer($clinique);
            $this->line("  Référentiel amorcé : {$clinique->nom} (#{$clinique->id})");
        }

        $this->info("{$cliniques->count()} clinique(s) traitée(s).");

        return self::SUCCESS;
    }
}
