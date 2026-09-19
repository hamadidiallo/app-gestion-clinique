<?php

use App\Models\Assurance;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;

function getTestCaissierUser(): User
{
    $role = Role::firstOrCreate(['nom' => 'Caissier']);

    return User::firstOrCreate(
        ['email' => 'caissiere_quick_test@clinique.local'],
        [
            'nom' => 'Sylla',
            'prenom' => 'Fatoumata',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]
    );
}

test('caissier can view quick patient modal triggers on tickets create screen', function () {
    $caissier = getTestCaissierUser();

    $response = $this->actingAs($caissier)->get(route('tickets.create'));

    $response->assertStatus(200)
        ->assertSee('quickPatientModal')
        ->assertSee('Nouveau Patient')
        ->assertSee('quick_patient_form');
});

test('caissier can quickly create a private patient via ajax and receive json', function () {
    $caissier = getTestCaissierUser();

    $patientData = [
        'prenom' => 'Ousmane',
        'nom' => 'Coulibaly',
        'sexe' => 'M',
        'telephone' => '76543210',
        'statut' => 'non_assure',
    ];

    $response = $this->actingAs($caissier)
        ->postJson(route('patients.store'), $patientData);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'patient' => [
                'prenom' => 'Ousmane',
                'nom' => 'Coulibaly',
                'nom_complet' => 'Ousmane Coulibaly',
                'sexe' => 'M',
                'telephone' => '76543210',
                'statut' => 'non_assure',
            ],
        ]);

    $this->assertDatabaseHas('patients', [
        'prenom' => 'Ousmane',
        'nom' => 'Coulibaly',
        'telephone' => '76543210',
    ]);
});

test('caissier can quickly create an assured patient via ajax with insurance card', function () {
    $caissier = getTestCaissierUser();

    $assurance = Assurance::firstOrCreate(
        ['nom' => 'CANAM Mali'],
        ['code' => 'CANAM', 'taux_par_defaut' => 80, 'statut' => true]
    );

    $patientData = [
        'prenom' => 'Mariam',
        'nom' => 'Diarra',
        'sexe' => 'F',
        'telephone' => '65432109',
        'statut' => 'assure',
        'assurance_id' => $assurance->id,
        'taux_couverture' => 80,
        'numero_assure' => 'CANAM-776655',
    ];

    $response = $this->actingAs($caissier)
        ->postJson(route('patients.store'), $patientData);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'patient' => [
                'prenom' => 'Mariam',
                'nom' => 'Diarra',
                'nom_complet' => 'Mariam Diarra',
                'statut' => 'assure',
                'assurance_id' => $assurance->id,
                'numero_assure' => 'CANAM-776655',
                'taux_couverture' => 80.0,
            ],
        ]);

    $patient = Patient::where('nom', 'Diarra')->where('prenom', 'Mariam')->first();
    expect($patient)->not->toBeNull();
    expect($patient->cartesAssurance()->count())->toBe(1);
    expect($patient->cartesAssurance->first()->reference)->toBe('CANAM-776655');
});

test('quick patient validation returns 422 json when required fields are missing', function () {
    $caissier = getTestCaissierUser();

    $response = $this->actingAs($caissier)
        ->postJson(route('patients.store'), [
            'prenom' => '',
            'nom' => '',
            'sexe' => 'X',
            'statut' => 'invalide',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['prenom', 'nom', 'sexe', 'statut']);
});
