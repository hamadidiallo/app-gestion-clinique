<?php

/**
 * Services medicaux proposes par defaut a chaque nouvelle clinique.
 */

return [
    [
        'code' => 'MED-GEN',
        'nom' => 'Médecine Générale', 'description' => 'Consultations de routine et urgences médicales générales', 'statut' => true,
    ],
    [
        'code' => 'GYN-OBS',
        'nom' => 'Gynécologie - Obstétrique', 'description' => 'Suivi de grossesse, maternité et santé de la femme', 'statut' => true,
    ],
    [
        'code' => 'PED',
        'nom' => 'Pédiatrie', 'description' => 'Soins et consultations pour nouveau-nés, enfants et adolescents', 'statut' => true,
    ],
    [
        'code' => 'RAD-IMG',
        'nom' => 'Imagerie Médicale & Échographie', 'description' => 'Examens radiologiques, échographies obstétricales et abdominales', 'statut' => true,
    ],
    [
        'code' => 'LAB-BIO',
        'nom' => 'Laboratoire d\'Analyses Médicales', 'description' => 'Analyses biologiques, hématologie, biochimie', 'statut' => true,
    ],
    [
        'code' => 'CHIR',
        'nom' => 'Chirurgie Générale & Spécialisée', 'description' => 'Bloc opératoire et interventions chirurgicales', 'statut' => true,
    ],
    [
        'code' => 'SOINS-URG',
        'nom' => 'Soins Infirmiers & Urgences', 'description' => 'Injections, pansements, perfusions et premiers soins d\'urgence', 'statut' => true,
    ],
];
