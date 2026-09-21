<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Amorce la base avec les seules données de référence de l'application.
     *
     * Aucun jeu de démonstration n'est posé ici : les comptes de test et la
     * clinique fictive ont été retirés. La commande peut donc être exécutée
     * en production sans précaution particulière.
     */
    public function run(): void
    {
        // Référentiels communs à toute la plateforme, indispensables au fonctionnement :
        // sans rôles, aucun collaborateur ne peut être rattaché ; sans modes de paiement,
        // aucun encaissement ne peut être enregistré.
        $this->call([
            RoleSeeder::class,
            ModePaiementSeeder::class,
        ]);

        // Référentiel propre à chaque clinique (services, actes, assurances, dépenses).
        // Les nouvelles cliniques sont dotées automatiquement à l'inscription ; cet appel
        // ne concerne donc que les établissements déjà enregistrés. Il est sans effet
        // sur une base neuve.
        $this->call([
            ReferentielCliniquesSeeder::class,
        ]);
    }
}
