<?php

namespace Database\Seeders;

use App\Models\ModePaiement;
use Illuminate\Database\Seeder;

class ModePaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $modes = [
            [
                'nom' => 'Espèces',
                'code' => 'ESPECES',
                'description' => 'Paiement en espèces',
            ],
            [
                'nom' => 'Orange Money',
                'code' => 'ORANGE_MONEY',
                'description' => 'Paiement par Orange Money',
            ],
            [
                'nom' => 'Moov Money',
                'code' => 'MOOV_MONEY',
                'description' => 'Paiement par Moov Money',
            ],
            [
                'nom' => 'Carte bancaire',
                'code' => 'CARTE',
                'description' => 'Paiement par carte bancaire',
            ],
            [
                'nom' => 'Virement bancaire',
                'code' => 'VIREMENT',
                'description' => 'Paiement par virement bancaire',
            ],
            [
                'nom' => 'Chèque',
                'code' => 'CHEQUE',
                'description' => 'Paiement par chèque',
            ],
        ];
        foreach ($modes as $mode) {
            ModePaiement::updateOrCreate(
                ['code' => $mode['code']],
                $mode
            );
        }
    }
}
