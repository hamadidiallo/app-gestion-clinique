<?php

namespace Database\Seeders;

use App\Models\Acte;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ActeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'MED-GEN' => Service::firstOrCreate(
                ['code' => 'MED-GEN'],
                ['nom' => 'Médecine Générale', 'description' => 'Consultations de routine et urgences médicales générales', 'statut' => true]
            ),
            'GYN-OBS' => Service::firstOrCreate(
                ['code' => 'GYN-OBS'],
                ['nom' => 'Gynécologie - Obstétrique', 'description' => 'Suivi de grossesse, maternité et santé de la femme', 'statut' => true]
            ),
            'PED' => Service::firstOrCreate(
                ['code' => 'PED'],
                ['nom' => 'Pédiatrie', 'description' => 'Soins et consultations pour nouveau-nés, enfants et adolescents', 'statut' => true]
            ),
            'RAD-IMG' => Service::firstOrCreate(
                ['code' => 'RAD-IMG'],
                ['nom' => 'Imagerie Médicale & Échographie', 'description' => 'Examens radiologiques, échographies obstétricales et abdominales', 'statut' => true]
            ),
            'LAB-BIO' => Service::firstOrCreate(
                ['code' => 'LAB-BIO'],
                ['nom' => 'Laboratoire d\'Analyses Médicales', 'description' => 'Analyses biologiques, hématologie, biochimie', 'statut' => true]
            ),
            'CHIR' => Service::firstOrCreate(
                ['code' => 'CHIR'],
                ['nom' => 'Chirurgie Générale & Spécialisée', 'description' => 'Bloc opératoire et interventions chirurgicales', 'statut' => true]
            ),
            'SOINS-URG' => Service::firstOrCreate(
                ['code' => 'SOINS-URG'],
                ['nom' => 'Soins Infirmiers & Urgences', 'description' => 'Injections, pansements, perfusions et premiers soins d\'urgence', 'statut' => true]
            ),
        ];

        $actes = [
            // Médecine Générale
            [
                'service_code' => 'MED-GEN',
                'code' => 'ACT-CGEN-01',
                'nom' => 'Consultation Médecine Générale',
                'categorie' => 'consultation',
                'tarif_normal' => 5000,
                'tarif_amo' => 4000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 60.00,
                'part_clinique_pourcentage' => 40.00,
                'description' => 'Consultation médicale standard au cabinet.',
            ],
            [
                'service_code' => 'MED-GEN',
                'code' => 'ACT-CGEN-02',
                'nom' => 'Consultation d\'Urgence de Nuit / Férié',
                'categorie' => 'consultation',
                'tarif_normal' => 7500,
                'tarif_amo' => 6000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 65.00,
                'part_clinique_pourcentage' => 35.00,
                'description' => 'Consultation d\'urgence hors heures ouvrables.',
            ],
            [
                'service_code' => 'MED-GEN',
                'code' => 'ACT-ECG-01',
                'nom' => 'Électrocardiogramme (ECG) avec interprétation',
                'categorie' => 'exploration',
                'tarif_normal' => 10000,
                'tarif_amo' => 8000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 50.00,
                'part_clinique_pourcentage' => 50.00,
                'description' => 'Enregistrement de l\'activité électrique du cœur.',
            ],

            // Gynécologie / Obstétrique
            [
                'service_code' => 'GYN-OBS',
                'code' => 'ACT-CGYN-01',
                'nom' => 'Consultation Gynécologique Spécialisée',
                'categorie' => 'consultation',
                'tarif_normal' => 10000,
                'tarif_amo' => 8000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 70.00,
                'part_clinique_pourcentage' => 30.00,
                'description' => 'Consultation gynécologique par médecin spécialiste.',
            ],
            [
                'service_code' => 'GYN-OBS',
                'code' => 'ACT-CPN-01',
                'nom' => 'Consultation Prénatale (CPN)',
                'categorie' => 'consultation',
                'tarif_normal' => 3500,
                'tarif_amo' => 3000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 50.00,
                'part_clinique_pourcentage' => 50.00,
                'description' => 'Suivi régulier de grossesse.',
            ],
            [
                'service_code' => 'GYN-OBS',
                'code' => 'ACT-ACCH-01',
                'nom' => 'Accouchement Eutocique Normal',
                'categorie' => 'maternite',
                'tarif_normal' => 45000,
                'tarif_amo' => 35000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 50.00,
                'part_clinique_pourcentage' => 50.00,
                'description' => 'Prise en charge du travail et délivrance par voie basse.',
            ],
            [
                'service_code' => 'GYN-OBS',
                'code' => 'ACT-CESAR-01',
                'nom' => 'Césarienne Programmée ou d\'Urgence',
                'categorie' => 'chirurgie',
                'tarif_normal' => 120000,
                'tarif_amo' => 95000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 55.00,
                'part_clinique_pourcentage' => 45.00,
                'description' => 'Intervention chirurgicale obstétricale au bloc.',
            ],

            // Pédiatrie
            [
                'service_code' => 'PED',
                'code' => 'ACT-CPED-01',
                'nom' => 'Consultation Pédiatrique',
                'categorie' => 'consultation',
                'tarif_normal' => 7000,
                'tarif_amo' => 5500,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 65.00,
                'part_clinique_pourcentage' => 35.00,
                'description' => 'Consultation par pédiatre.',
            ],

            // Imagerie Médicale
            [
                'service_code' => 'RAD-IMG',
                'code' => 'ACT-ECHO-OBS',
                'nom' => 'Échographie Obstétricale T1 / T2 / T3',
                'categorie' => 'imagerie',
                'tarif_normal' => 15000,
                'tarif_amo' => 12000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 50.00,
                'part_clinique_pourcentage' => 50.00,
                'description' => 'Échographie de surveillance fœtale.',
            ],
            [
                'service_code' => 'RAD-IMG',
                'code' => 'ACT-ECHO-ABD',
                'nom' => 'Échographie Abdomino-Pelvienne',
                'categorie' => 'imagerie',
                'tarif_normal' => 18000,
                'tarif_amo' => 15000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 50.00,
                'part_clinique_pourcentage' => 50.00,
                'description' => 'Exploration échographique des organes abdominaux et pelviens.',
            ],

            // Laboratoire
            [
                'service_code' => 'LAB-BIO',
                'code' => 'ACT-NFS-01',
                'nom' => 'Numération Formule Sanguine (NFS / Hémogramme)',
                'categorie' => 'biologie',
                'tarif_normal' => 6000,
                'tarif_amo' => 4500,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 20.00,
                'part_clinique_pourcentage' => 80.00,
                'description' => 'Bilan hématologique complet.',
            ],
            [
                'service_code' => 'LAB-BIO',
                'code' => 'ACT-GE-01',
                'nom' => 'Goutte Épaisse / Test Rapide Paludisme (GE/TDR)',
                'categorie' => 'biologie',
                'tarif_normal' => 2500,
                'tarif_amo' => 2000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 15.00,
                'part_clinique_pourcentage' => 85.00,
                'description' => 'Dépistage et quantification du paludisme.',
            ],
            [
                'service_code' => 'LAB-BIO',
                'code' => 'ACT-GLYC-01',
                'nom' => 'Glycémie à jeun',
                'categorie' => 'biologie',
                'tarif_normal' => 2000,
                'tarif_amo' => 1500,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 15.00,
                'part_clinique_pourcentage' => 85.00,
                'description' => 'Dosage du taux de sucre dans le sang.',
            ],

            // Chirurgie
            [
                'service_code' => 'CHIR',
                'code' => 'ACT-CHIR-APP',
                'nom' => 'Appendicectomie sous anesthésie générale',
                'categorie' => 'chirurgie',
                'tarif_normal' => 150000,
                'tarif_amo' => 120000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 50.00,
                'part_clinique_pourcentage' => 50.00,
                'description' => 'Ablation chirurgicale de l\'appendice.',
            ],
            [
                'service_code' => 'CHIR',
                'code' => 'ACT-CHIR-HERN',
                'nom' => 'Cure de Hernie Inguinale',
                'categorie' => 'chirurgie',
                'tarif_normal' => 110000,
                'tarif_amo' => 85000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 50.00,
                'part_clinique_pourcentage' => 50.00,
                'description' => 'Réparation de paroi herniaire.',
            ],

            // Soins & Urgences
            [
                'service_code' => 'SOINS-URG',
                'code' => 'ACT-INJ-IM',
                'nom' => 'Injection Intra-Musculaire (IM) / Sous-Cutanée',
                'categorie' => 'soins',
                'tarif_normal' => 1000,
                'tarif_amo' => 800,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 10.00,
                'part_clinique_pourcentage' => 90.00,
                'description' => 'Administration médicamenteuse par voie parentérale.',
            ],
            [
                'service_code' => 'SOINS-URG',
                'code' => 'ACT-PERF-01',
                'nom' => 'Pose de Perfusion Intra-Veineuse avec surveillance',
                'categorie' => 'soins',
                'tarif_normal' => 3500,
                'tarif_amo' => 2500,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 20.00,
                'part_clinique_pourcentage' => 80.00,
                'description' => 'Mise en place d\'une voie veineuse périphérique.',
            ],
            [
                'service_code' => 'SOINS-URG',
                'code' => 'ACT-PANS-01',
                'nom' => 'Pansement Simple / Nettoyage de plaie',
                'categorie' => 'soins',
                'tarif_normal' => 2500,
                'tarif_amo' => 2000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 20.00,
                'part_clinique_pourcentage' => 80.00,
                'description' => 'Asepsie et réfection de pansement stérile.',
            ],
            [
                'service_code' => 'SOINS-URG',
                'code' => 'ACT-SUT-01',
                'nom' => 'Suture de Plaie Simple sous anesthésie locale',
                'categorie' => 'soins',
                'tarif_normal' => 8000,
                'tarif_amo' => 6000,
                'tarif_specifique' => null,
                'part_medecin_pourcentage' => 40.00,
                'part_clinique_pourcentage' => 60.00,
                'description' => 'Fermeture de berges cutanées par points de suture.',
            ],
        ];

        foreach ($actes as $item) {
            $service = $services[$item['service_code']];

            Acte::updateOrCreate(
                ['code' => $item['code']],
                [
                    'service_id' => $service->id,
                    'nom' => $item['nom'],
                    'categorie' => $item['categorie'],
                    'tarif_normal' => $item['tarif_normal'],
                    'tarif_amo' => $item['tarif_amo'],
                    'tarif_specifique' => $item['tarif_specifique'],
                    'part_medecin_pourcentage' => $item['part_medecin_pourcentage'],
                    'part_clinique_pourcentage' => $item['part_clinique_pourcentage'],
                    'statut' => true,
                    'description' => $item['description'],
                ]
            );
        }
    }
}
