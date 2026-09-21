<?php

namespace Database\Seeders;

use App\Models\Clinique;
use App\Services\AmorcageCliniqueService;
use Illuminate\Database\Seeder;

/**
 * Amorce le référentiel de chaque clinique enregistrée.
 *
 * Les catalogues (services, actes, assurances, catégories de dépenses) appartiennent
 * à la clinique et non à la plateforme : les créer sans clinique de rattachement
 * produirait des lignes qu'aucun utilisateur ne verrait, le cloisonnement filtrant
 * sur clinique_id. Les données elles-mêmes vivent dans database/data.
 */
class ReferentielCliniquesSeeder extends Seeder
{
    public function run(): void
    {
        $amorcage = app(AmorcageCliniqueService::class);

        Clinique::query()->orderBy('id')->each(function (Clinique $clinique) use ($amorcage) {
            $amorcage->amorcer($clinique);
        });
    }
}
