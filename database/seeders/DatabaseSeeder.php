<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Données de référence partagées par toute la plateforme, sans donnée fictive :
        // les rôles et les modes de paiement ne sont pas rattachés à une clinique.
        // Elles doivent exister aussi en production.
        $this->call([
            RoleSeeder::class,
            ModePaiementSeeder::class,
        ]);

        // Jeux de démonstration : comptes de test, clinique fictive et données d'exemple.
        // Ils ne doivent JAMAIS être exécutés en production (comptes à mot de passe connu
        // et rattachés à aucune clinique, donc non cloisonnés).
        if (app()->environment('production')) {
            $this->command?->warn('Environnement de production détecté : les seeders de démonstration ont été ignorés.');

            return;
        }

        $this->call([
            UserSeeder::class,
            // Crée la clinique de démonstration et y rattache les données orphelines
            SaaSMigrationSeeder::class,
            // Doit suivre la création de la clinique : le référentiel est amorcé par clinique
            ReferentielCliniquesSeeder::class,
        ]);
    }
}
