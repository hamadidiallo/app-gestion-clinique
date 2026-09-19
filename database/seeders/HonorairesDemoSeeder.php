<?php

namespace Database\Seeders;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Prestation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HonorairesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $drAwa = Medecin::firstOrCreate(
            ['telephone' => '+22370000001'],
            [
                'code' => 'DR-TRAO-002',
                'nom' => 'TRAORÉ',
                'prenom' => 'Awa',
                'specialite' => 'Gynécologie-Obstétrique',
                'type_remuneration' => 'pourcentage',
                'pourcentage' => 50.00,
                'statut' => true,
            ]
        );

        $drDiallo = Medecin::first();
        $patient1 = Patient::first();
        $patient2 = Patient::skip(1)->first();

        if ($drDiallo && $patient1) {
            Prestation::firstOrCreate(
                ['reference' => 'SOIN-20260918-0003'],
                [
                    'patient_id' => $patient1->id,
                    'medecin_id' => $drDiallo->id,
                    'service_id' => 1,
                    'type' => 'Échographie Abdominale Complète',
                    'montant' => 15000,
                    'pourcentage_medecin' => 40,
                    'pourcentage_clinique' => 60,
                    'part_medecin' => 6000,
                    'part_clinique' => 9000,
                    'date_prestation' => Carbon::now()->subHours(3),
                    'statut' => true,
                ]
            );
        }

        if ($patient2) {
            Prestation::firstOrCreate(
                ['reference' => 'SOIN-20260918-0004'],
                [
                    'patient_id' => $patient2->id,
                    'medecin_id' => $drAwa->id,
                    'service_id' => 2,
                    'type' => 'Consultation Gynécologique & Écho',
                    'montant' => 12000,
                    'pourcentage_medecin' => 50,
                    'pourcentage_clinique' => 50,
                    'part_medecin' => 6000,
                    'part_clinique' => 6000,
                    'date_prestation' => Carbon::now()->subHours(1),
                    'statut' => true,
                ]
            );
        }
    }
}
