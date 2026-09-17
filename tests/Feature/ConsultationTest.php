<?php

use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Ordonnance;
use App\Models\OrdonnanceLigne;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;

function getOrCreateTestUser(): User
{
    $role = Role::firstOrCreate(['nom' => 'Administrateur']);

    return User::firstOrCreate(
        ['email' => 'admin@clinique.local'],
        [
            'nom' => 'Admin',
            'prenom' => 'Principal',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]
    );
}

test('authenticated user can view consultations index', function () {
    $user = getOrCreateTestUser();

    $response = $this->actingAs($user)->get(route('consultations.index'));

    $response->assertStatus(200);
});

test('can create a consultation with vital signs and prescriptions', function () {
    $user = getOrCreateTestUser();
    $patient = Patient::create([
        'nom' => 'Traore',
        'prenom' => 'Sekou',
        'sexe' => 'M',
        'telephone' => '76123456',
        'statut' => 'non_assure',
    ]);
    $medecin = Medecin::first() ?? Medecin::create([
        'nom' => 'Coulibaly',
        'prenom' => 'Drissa',
        'specialite' => 'Médecine Générale',
        'type_remuneration' => 'pourcentage',
        'pourcentage' => 30.00,
        'statut' => true,
    ]);

    $postData = [
        'patient_id' => $patient->id,
        'medecin_id' => $medecin->id,
        'date_consultation' => now()->format('Y-m-d H:i:s'),
        'tension_arterielle' => '13/8',
        'temperature' => 38.5,
        'poids' => 74.0,
        'taille' => 178,
        'pouls' => 85,
        'glycemie' => 0.95,
        'groupe_sanguin' => 'O+',
        'allergies' => 'Pénicilline',
        'antecedents_personnels' => 'HTA',
        'motif_consultation' => 'Fièvre aiguë et frissons',
        'histoire_maladie' => 'Fièvre depuis 3 jours avec courbatures',
        'diagnostic' => 'Accès palustre simple',
        'conduite_a_tenir' => 'Repos 48h et hydratation',
        'prescriptions' => [
            [
                'medicament' => 'Artéméther + Luméfantrine',
                'forme' => 'Comprimé',
                'dosage' => '20/120mg',
                'posologie' => '4 comprimés par prise selon le schéma de 6 doses',
                'duree' => '3 jours',
                'instructions' => 'Prendre avec un aliment gras ou du lait',
            ],
            [
                'medicament' => 'Paracétamol',
                'forme' => 'Comprimé',
                'dosage' => '1000mg',
                'posologie' => '1 comprimé toutes les 8h en cas de fièvre > 38°C',
                'duree' => '3 jours',
                'instructions' => 'Ne pas dépasser 3g par 24h',
            ],
        ],
        'instructions_generales' => 'Revoir en consultation si la fièvre persiste après 48h.',
    ];

    $response = $this->actingAs($user)->post(route('consultations.store'), $postData);

    $consultation = Consultation::where('patient_id', $patient->id)->first();
    expect($consultation)->not->toBeNull();
    expect($consultation->tension_arterielle)->toBe('13/8');
    expect($consultation->diagnostic)->toBe('Accès palustre simple');

    // Vérification du dossier médical
    $patient->refresh();
    expect($patient->dossierMedical)->not->toBeNull();
    expect($patient->dossierMedical->groupe_sanguin)->toBe('O+');
    expect($patient->dossierMedical->allergies)->toBe('Pénicilline');

    // Vérification de l'ordonnance et de ses lignes
    expect($consultation->ordonnance)->not->toBeNull();
    expect($consultation->ordonnance->lignes)->toHaveCount(2);

    $response->assertRedirect(route('consultations.show', $consultation));
});

test('can print medical prescription', function () {
    $user = getOrCreateTestUser();
    $patient = Patient::create([
        'nom' => 'Diallo',
        'prenom' => 'Fatoumata',
        'sexe' => 'F',
        'telephone' => '70123456',
        'statut' => 'non_assure',
    ]);

    $consultation = Consultation::create([
        'patient_id' => $patient->id,
        'reference' => 'CS-20260915-TEST',
        'date_consultation' => now(),
        'motif_consultation' => 'Douleurs abdominales',
        'diagnostic' => 'Gastrite aiguë',
    ]);

    $ordonnance = Ordonnance::create([
        'consultation_id' => $consultation->id,
        'patient_id' => $patient->id,
        'reference' => 'ORD-20260915-TEST',
        'date_ordonnance' => now()->toDateString(),
    ]);

    OrdonnanceLigne::create([
        'ordonnance_id' => $ordonnance->id,
        'medicament' => 'Oméprazole',
        'dosage' => '20mg',
        'posologie' => '1 gélule le matin à jeun',
        'duree' => '14 jours',
    ]);

    $response = $this->actingAs($user)->get(route('consultations.print-ordonnance', $consultation));

    $response->assertStatus(200);
    $response->assertSee('Ordonnance Médicale');
    $response->assertSee('Oméprazole');
    $response->assertSee('Fatoumata');
});
