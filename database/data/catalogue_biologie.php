<?php

/*
|--------------------------------------------------------------------------
| Catalogue de départ des examens de laboratoire
|--------------------------------------------------------------------------
|
| Examens courants des cliniques d'Afrique de l'Ouest, avec leurs paramètres et des
| valeurs usuelles tirées des références de biologie clinique. Ces valeurs varient selon
| la méthode, l'automate et la population : chaque laboratoire doit les vérifier et les
| ajuster avant de les utiliser.
|
| Les tarifs ne sont pas fournis : un examen importé sans tarif reste inactif tant que
| la clinique ne l'a pas renseigné. Les codes ACT-NFS-01, ACT-GE-01 et ACT-GLYC-01
| correspondent aux examens créés au démarrage de chaque clinique : l'import leur
| ajoute leurs paramètres au lieu de les dupliquer.
|
| Bornes : valeur_min / valeur_max (générales), et facultativement _homme, _femme, _enfant.
|
*/

$aVerifier = 'Valeurs usuelles à vérifier par votre laboratoire.';

$negatif = fn (string $nom) => ['nom' => $nom, 'type' => 'texte', 'valeur_normale_texte' => 'Négatif'];

return [
    [
        'code' => 'ACT-NFS-01',
        'nom' => 'Numération Formule Sanguine (NFS / Hémogramme)',
        'description' => 'Bilan hématologique complet. '.$aVerifier,
        'parametres' => [
            ['nom' => 'Hémoglobine', 'unite' => 'g/dL', 'valeur_min' => 12, 'valeur_max' => 17, 'valeur_min_homme' => 13, 'valeur_max_homme' => 17, 'valeur_min_femme' => 12, 'valeur_max_femme' => 16, 'valeur_min_enfant' => 11, 'valeur_max_enfant' => 14],
            ['nom' => 'Hématocrite', 'unite' => '%', 'valeur_min' => 37, 'valeur_max' => 54, 'valeur_min_homme' => 40, 'valeur_max_homme' => 54, 'valeur_min_femme' => 37, 'valeur_max_femme' => 47, 'valeur_min_enfant' => 33, 'valeur_max_enfant' => 42],
            ['nom' => 'Globules rouges', 'unite' => 'T/L', 'valeur_min' => 4.0, 'valeur_max' => 5.9, 'valeur_min_homme' => 4.5, 'valeur_max_homme' => 5.9, 'valeur_min_femme' => 4.0, 'valeur_max_femme' => 5.2],
            ['nom' => 'VGM', 'unite' => 'fL', 'valeur_min' => 80, 'valeur_max' => 100],
            ['nom' => 'TCMH', 'unite' => 'pg', 'valeur_min' => 27, 'valeur_max' => 32],
            ['nom' => 'CCMH', 'unite' => 'g/dL', 'valeur_min' => 32, 'valeur_max' => 36],
            ['nom' => 'Leucocytes', 'unite' => 'G/L', 'valeur_min' => 4, 'valeur_max' => 10, 'valeur_min_enfant' => 4.5, 'valeur_max_enfant' => 13.5],
            ['nom' => 'Polynucléaires neutrophiles', 'unite' => 'G/L', 'valeur_min' => 1.5, 'valeur_max' => 7],
            ['nom' => 'Lymphocytes', 'unite' => 'G/L', 'valeur_min' => 1, 'valeur_max' => 4],
            ['nom' => 'Plaquettes', 'unite' => 'G/L', 'valeur_min' => 150, 'valeur_max' => 400],
        ],
    ],
    [
        'code' => 'ACT-GE-01',
        'nom' => 'Goutte Épaisse / Test Rapide Paludisme (GE/TDR)',
        'description' => 'Dépistage et quantification du paludisme. '.$aVerifier,
        'parametres' => [
            $negatif('TDR paludisme'),
            $negatif('Goutte épaisse'),
            ['nom' => 'Densité parasitaire', 'unite' => 'parasites/µL', 'valeur_min' => 0, 'valeur_max' => 0],
        ],
    ],
    [
        'code' => 'ACT-GLYC-01',
        'nom' => 'Glycémie à jeun',
        'description' => 'Dosage du taux de sucre dans le sang. '.$aVerifier,
        'parametres' => [
            ['nom' => 'Glycémie à jeun', 'unite' => 'g/L', 'valeur_min' => 0.70, 'valeur_max' => 1.10],
        ],
    ],
    [
        'code' => 'ACT-HBA1C-01',
        'nom' => 'Hémoglobine glyquée (HbA1c)',
        'description' => 'Équilibre glycémique des trois derniers mois. '.$aVerifier,
        'parametres' => [
            ['nom' => 'HbA1c', 'unite' => '%', 'valeur_min' => 4, 'valeur_max' => 6],
        ],
    ],
    [
        'code' => 'ACT-CRP-01',
        'nom' => 'Protéine C réactive (CRP)',
        'description' => 'Marqueur de l\'inflammation. '.$aVerifier,
        'parametres' => [
            ['nom' => 'CRP', 'unite' => 'mg/L', 'valeur_min' => 0, 'valeur_max' => 6],
        ],
    ],
    [
        'code' => 'ACT-VS-01',
        'nom' => 'Vitesse de sédimentation (VS)',
        'description' => $aVerifier,
        'parametres' => [
            ['nom' => 'VS 1re heure', 'unite' => 'mm', 'valeur_min' => 0, 'valeur_max' => 20, 'valeur_max_homme' => 15, 'valeur_min_homme' => 0, 'valeur_min_femme' => 0, 'valeur_max_femme' => 20],
        ],
    ],
    [
        'code' => 'ACT-CREA-01',
        'nom' => 'Créatininémie',
        'description' => 'Fonction rénale. '.$aVerifier,
        'parametres' => [
            ['nom' => 'Créatinine', 'unite' => 'mg/L', 'valeur_min' => 5, 'valeur_max' => 13, 'valeur_min_homme' => 7, 'valeur_max_homme' => 13, 'valeur_min_femme' => 5, 'valeur_max_femme' => 11],
        ],
    ],
    [
        'code' => 'ACT-UREE-01',
        'nom' => 'Urémie',
        'description' => 'Fonction rénale. '.$aVerifier,
        'parametres' => [
            ['nom' => 'Urée', 'unite' => 'g/L', 'valeur_min' => 0.15, 'valeur_max' => 0.45],
        ],
    ],
    [
        'code' => 'ACT-URIC-01',
        'nom' => 'Uricémie (acide urique)',
        'description' => $aVerifier,
        'parametres' => [
            ['nom' => 'Acide urique', 'unite' => 'mg/L', 'valeur_min' => 25, 'valeur_max' => 70, 'valeur_min_homme' => 35, 'valeur_max_homme' => 70, 'valeur_min_femme' => 25, 'valeur_max_femme' => 60],
        ],
    ],
    [
        'code' => 'ACT-TRANS-01',
        'nom' => 'Transaminases (ASAT / ALAT)',
        'description' => 'Bilan hépatique. '.$aVerifier,
        'parametres' => [
            ['nom' => 'ASAT (TGO)', 'unite' => 'UI/L', 'valeur_min' => 0, 'valeur_max' => 40, 'valeur_min_femme' => 0, 'valeur_max_femme' => 35],
            ['nom' => 'ALAT (TGP)', 'unite' => 'UI/L', 'valeur_min' => 0, 'valeur_max' => 40, 'valeur_min_femme' => 0, 'valeur_max_femme' => 35],
        ],
    ],
    [
        'code' => 'ACT-BILI-01',
        'nom' => 'Bilirubine (totale et conjuguée)',
        'description' => 'Bilan hépatique. '.$aVerifier,
        'parametres' => [
            ['nom' => 'Bilirubine totale', 'unite' => 'mg/L', 'valeur_min' => 0, 'valeur_max' => 12],
            ['nom' => 'Bilirubine conjuguée', 'unite' => 'mg/L', 'valeur_min' => 0, 'valeur_max' => 3],
        ],
    ],
    [
        'code' => 'ACT-LIPI-01',
        'nom' => 'Bilan lipidique',
        'description' => 'Cholestérol et triglycérides. '.$aVerifier,
        'parametres' => [
            ['nom' => 'Cholestérol total', 'unite' => 'g/L', 'valeur_max' => 2.00],
            ['nom' => 'Cholestérol HDL', 'unite' => 'g/L', 'valeur_min' => 0.40, 'valeur_min_homme' => 0.40, 'valeur_min_femme' => 0.50],
            ['nom' => 'Cholestérol LDL', 'unite' => 'g/L', 'valeur_max' => 1.60],
            ['nom' => 'Triglycérides', 'unite' => 'g/L', 'valeur_max' => 1.50],
        ],
    ],
    [
        'code' => 'ACT-IONO-01',
        'nom' => 'Ionogramme sanguin',
        'description' => $aVerifier,
        'parametres' => [
            ['nom' => 'Sodium', 'unite' => 'mmol/L', 'valeur_min' => 135, 'valeur_max' => 145],
            ['nom' => 'Potassium', 'unite' => 'mmol/L', 'valeur_min' => 3.5, 'valeur_max' => 5.0],
            ['nom' => 'Chlore', 'unite' => 'mmol/L', 'valeur_min' => 98, 'valeur_max' => 107],
            ['nom' => 'Bicarbonates', 'unite' => 'mmol/L', 'valeur_min' => 22, 'valeur_max' => 29],
        ],
    ],
    [
        'code' => 'ACT-CALC-01',
        'nom' => 'Calcémie',
        'description' => $aVerifier,
        'parametres' => [
            ['nom' => 'Calcium', 'unite' => 'mg/L', 'valeur_min' => 85, 'valeur_max' => 105],
        ],
    ],
    [
        'code' => 'ACT-WIDAL-01',
        'nom' => 'Sérodiagnostic de Widal et Félix',
        'description' => 'Fièvre typhoïde. '.$aVerifier,
        'parametres' => [
            $negatif('Antigène O (TO)'),
            $negatif('Antigène H (TH)'),
        ],
    ],
    [
        'code' => 'ACT-GSRH-01',
        'nom' => 'Groupage sanguin ABO / Rhésus',
        'description' => 'Résultat à recopier tel quel : pas de valeur normale.',
        'parametres' => [
            ['nom' => 'Groupe ABO', 'type' => 'texte'],
            ['nom' => 'Rhésus (D)', 'type' => 'texte'],
        ],
    ],
    [
        'code' => 'ACT-EMMEL-01',
        'nom' => 'Test d\'Emmel (falciformation)',
        'description' => 'Dépistage de la drépanocytose. '.$aVerifier,
        'parametres' => [
            $negatif('Test d\'Emmel'),
        ],
    ],
    [
        'code' => 'ACT-BU-01',
        'nom' => 'Bandelette urinaire',
        'description' => $aVerifier,
        'parametres' => [
            $negatif('Leucocytes'),
            $negatif('Nitrites'),
            $negatif('Protéines'),
            $negatif('Glucose'),
            $negatif('Corps cétoniques'),
            $negatif('Sang'),
            ['nom' => 'pH', 'valeur_min' => 5, 'valeur_max' => 8],
        ],
    ],
    [
        'code' => 'ACT-KAOP-01',
        'nom' => 'Examen parasitologique des selles (KAOP)',
        'description' => 'Kystes, amibes, œufs et parasites. '.$aVerifier,
        'parametres' => [
            ['nom' => 'Résultat', 'type' => 'texte', 'valeur_normale_texte' => 'Absence de parasites'],
        ],
    ],
    [
        'code' => 'ACT-HCG-01',
        'nom' => 'Test de grossesse (β-hCG urinaire)',
        'description' => 'Résultat à recopier tel quel : pas de valeur normale.',
        'parametres' => [
            ['nom' => 'β-hCG urinaire', 'type' => 'texte'],
        ],
    ],
    [
        'code' => 'ACT-VIH-01',
        'nom' => 'Sérologie VIH',
        'description' => $aVerifier,
        'parametres' => [
            $negatif('Sérologie VIH 1 et 2'),
        ],
    ],
    [
        'code' => 'ACT-AGHBS-01',
        'nom' => 'Antigène HBs (hépatite B)',
        'description' => $aVerifier,
        'parametres' => [
            $negatif('Antigène HBs'),
        ],
    ],
    [
        'code' => 'ACT-SYPH-01',
        'nom' => 'Sérologie syphilitique (VDRL / TPHA)',
        'description' => $aVerifier,
        'parametres' => [
            $negatif('VDRL'),
            $negatif('TPHA'),
        ],
    ],
];
