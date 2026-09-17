<?php

namespace Database\Seeders;

use App\Models\CategorieDepense;
use Illuminate\Database\Seeder;

class CategorieDepenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Fournitures médicales',
                'code' => 'FOURN_MED',
                'description' => 'Gants, seringues, compresses et autres consommables médicaux',
            ],
            [
                'nom' => 'Équipements médicaux',
                'code' => 'EQUIP_MED',
                'description' => 'Achat et renouvellement des équipements médicaux',
            ],
            [
                'nom' => 'Maintenance',
                'code' => 'MAINTENANCE',
                'description' => 'Maintenance et réparation des équipements',
            ],
            [
                'nom' => 'Électricité',
                'code' => 'ELECTRICITE',
                'description' => 'Factures et dépenses liées à l’électricité',
            ],
            [
                'nom' => 'Eau',
                'code' => 'EAU',
                'description' => 'Factures et dépenses liées à l’eau',
            ],
            [
                'nom' => 'Loyer',
                'code' => 'LOYER',
                'description' => 'Loyer et charges locatives',
            ],
            [
                'nom' => 'Salaires',
                'code' => 'SALAIRES',
                'description' => 'Salaires et rémunérations du personnel',
            ],
            [
                'nom' => 'Nettoyage',
                'code' => 'NETTOYAGE',
                'description' => 'Produits et prestations de nettoyage',
            ],
            [
                'nom' => 'Transport',
                'code' => 'TRANSPORT',
                'description' => 'Frais de transport et déplacement',
            ],
            [
                'nom' => 'Autres dépenses',
                'code' => 'AUTRES',
                'description' => 'Dépenses diverses',
            ],
        ];

        foreach ($categories as $categorie) {
            CategorieDepense::updateOrCreate(
                ['code' => $categorie['code']],
                $categorie
            );
        }
    }
}
