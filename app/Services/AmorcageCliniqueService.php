<?php

namespace App\Services;

use App\Models\Acte;
use App\Models\Assurance;
use App\Models\CategorieDepense;
use App\Models\Clinique;
use App\Models\Scopes\BelongsToCliniqueScope;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

/**
 * Dote une clinique nouvellement créée de son référentiel de démarrage.
 *
 * Les données de référence (services, actes, assurances, catégories de dépenses)
 * appartiennent à chaque clinique et non à la plateforme : chaque établissement
 * fixe ses propres tarifs et conventions. Sans cet amorçage, une clinique qui
 * vient de s'inscrire démarre avec des catalogues vides et ne peut rien facturer.
 */
class AmorcageCliniqueService
{
    /**
     * Crée le référentiel de démarrage de la clinique.
     *
     * L'opération est idempotente : relancée, elle complète ce qui manque
     * sans dupliquer ni écraser ce que la clinique a déjà personnalisé.
     */
    public function amorcer(Clinique $clinique): void
    {
        DB::transaction(function () use ($clinique) {
            $services = $this->creerServices($clinique);
            $this->creerActes($clinique, $services);
            $this->creerAssurances($clinique);
            $this->creerCategoriesDepenses($clinique);
        });
    }

    /**
     * @return array<string, Service> les services indexés par code
     */
    private function creerServices(Clinique $clinique): array
    {
        $services = [];

        foreach ($this->catalogue('services') as $donnees) {
            $services[$donnees['code']] = Service::withoutGlobalScope(BelongsToCliniqueScope::class)
                ->firstOrCreate(
                    ['clinique_id' => $clinique->id, 'code' => $donnees['code']],
                    array_merge($donnees, ['clinique_id' => $clinique->id])
                );
        }

        return $services;
    }

    /**
     * @param  array<string, Service>  $services
     */
    private function creerActes(Clinique $clinique, array $services): void
    {
        foreach ($this->catalogue('actes') as $donnees) {
            $service = $services[$donnees['service_code']] ?? null;

            if (! $service) {
                continue;
            }

            Acte::withoutGlobalScope(BelongsToCliniqueScope::class)->firstOrCreate(
                ['clinique_id' => $clinique->id, 'code' => $donnees['code']],
                [
                    'clinique_id' => $clinique->id,
                    'service_id' => $service->id,
                    'nom' => $donnees['nom'],
                    'categorie' => $donnees['categorie'],
                    'tarif_normal' => $donnees['tarif_normal'],
                    'tarif_amo' => $donnees['tarif_amo'],
                    'tarif_specifique' => $donnees['tarif_specifique'],
                    'part_medecin_pourcentage' => $donnees['part_medecin_pourcentage'],
                    'part_clinique_pourcentage' => $donnees['part_clinique_pourcentage'],
                    'statut' => true,
                    'description' => $donnees['description'],
                ]
            );
        }
    }

    private function creerAssurances(Clinique $clinique): void
    {
        foreach ($this->catalogue('assurances') as $donnees) {
            Assurance::withoutGlobalScope(BelongsToCliniqueScope::class)->firstOrCreate(
                ['clinique_id' => $clinique->id, 'code' => $donnees['code']],
                array_merge($donnees, ['clinique_id' => $clinique->id])
            );
        }
    }

    private function creerCategoriesDepenses(Clinique $clinique): void
    {
        foreach ($this->catalogue('categories_depenses') as $donnees) {
            CategorieDepense::withoutGlobalScope(BelongsToCliniqueScope::class)->firstOrCreate(
                ['clinique_id' => $clinique->id, 'code' => $donnees['code']],
                array_merge($donnees, ['clinique_id' => $clinique->id])
            );
        }
    }

    /**
     * Charge un catalogue de référence depuis database/data.
     *
     * @return array<int, array<string, mixed>>
     */
    private function catalogue(string $nom): array
    {
        $chemin = database_path("data/{$nom}.php");

        return file_exists($chemin) ? require $chemin : [];
    }
}
