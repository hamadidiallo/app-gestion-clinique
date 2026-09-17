<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nom' => 'Administrateur',
                'description' => 'Accès complet à toutes les fonctionnalités et configurations de la clinique',
            ],
            [
                'nom' => 'Médecin',
                'description' => 'Consultations, actes médicaux et suivi des prestations',
            ],
            [
                'nom' => 'Caissier',
                'description' => 'Gestion de la caisse, encaissements des tickets et mouvements d\'espèces',
            ],
            [
                'nom' => 'Réceptionniste',
                'description' => 'Accueil des patients, enregistrement et création des tickets',
            ],
            [
                'nom' => 'Comptable',
                'description' => 'Gestion des dépenses, recettes, dettes et rémunérations',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['nom' => $role['nom']],
                $role
            );
        }
    }
}
