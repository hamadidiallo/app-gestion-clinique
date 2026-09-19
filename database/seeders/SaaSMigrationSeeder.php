<?php

namespace Database\Seeders;

use App\Models\Clinique;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaaSMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Création de la Clinique Initiale (Gahambani)
        $clinique = Clinique::firstOrCreate(
            ['slug' => 'clinique-gahambani'],
            [
                'nom' => 'Clinique Gahambani',
                'code' => 'GAH',
                'email' => 'contact@gahambani-sante.ml',
                'telephone' => '(+223) 20 22 00 00 / 76 00 00 00',
                'adresse' => 'Quartier Administratif',
                'ville' => 'Bamako',
                'pays' => 'Mali',
                'devise' => 'FCFA',
                'plan' => 'enterprise',
                'statut' => 'actif',
                'prefixe_ticket' => 'TCK',
                'prefixe_patient' => 'PAT',
            ]
        );

        // 2. Rapprochement de toutes les tables existantes vers la Clinique 1
        $tables = [
            'users',
            'patients',
            'services',
            'actes',
            'medecins',
            'tickets',
            'prestations',
            'caisses',
            'paiements',
            'dettes',
            'recettes',
            'depenses',
            'categorie_depenses',
            'assurances',
            'remunerations',
            'regle_partages',
            'consultations',
            'journal_activites',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'clinique_id')) {
                DB::table($table)->whereNull('clinique_id')->update(['clinique_id' => $clinique->id]);
            }
        }
    }
}
