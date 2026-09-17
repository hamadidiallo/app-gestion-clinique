<?php

use App\Models\Acte;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Prestation;
use App\Models\Remuneration;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $role = Role::firstOrCreate(['nom' => 'Admin']);
    $user = User::firstOrCreate(
        ['email' => 'admin_remun@test.com'],
        [
            'nom' => 'Admin',
            'prenom' => 'Test',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]
    );
    $this->actingAs($user);
});

test('peut afficher le registre des rémunérations', function () {
    $response = $this->get(route('remunerations.index'));
    $response->assertStatus(200);
});

test('peut prévisualiser les calculs de rétrocession via ajax', function () {
    $medecin = Medecin::create([
        'nom' => 'TestMed',
        'prenom' => 'Dr',
        'type_remuneration' => 'pourcentage',
        'pourcentage' => 60,
        'statut' => true,
    ]);

    $service = Service::create([
        'nom' => 'Chirurgie Test',
        'code' => 'CHIR-TEST',
        'statut' => true,
    ]);

    $acte = Acte::create([
        'service_id' => $service->id,
        'code' => 'ACT-TEST-01',
        'nom' => 'Opération Test',
        'tarif_normal' => 100000,
        'statut' => true,
    ]);

    $patient = Patient::create([
        'nom' => 'Patient',
        'prenom' => 'Test',
        'sexe' => 'M',
        'statut' => 'non_assure',
    ]);

    Prestation::create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'medecin_id' => $medecin->id,
        'acte_id' => $acte->id,
        'montant' => 100000,
        'part_medecin' => 60000,
        'part_clinique' => 40000,
        'date_prestation' => now(),
        'statut' => true,
    ]);

    $response = $this->getJson(route('remunerations.preview', [
        'medecin_id' => $medecin->id,
        'periode_debut' => now()->startOfMonth()->toDateString(),
        'periode_fin' => now()->endOfMonth()->toDateString(),
    ]));

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.nb_prestations', 1)
        ->assertJsonPath('data.montant_base', 100000)
        ->assertJsonPath('data.montant_medecin', 60000)
        ->assertJsonPath('data.montant_clinique', 40000);
});

test('peut afficher la fiche détaillée d une rémunération avec ses prestations', function () {
    $user = User::first();
    $medecin = Medecin::create([
        'nom' => 'DrTest2',
        'prenom' => 'Prenom',
        'type_remuneration' => 'pourcentage',
        'pourcentage' => 50,
        'statut' => true,
    ]);

    $remuneration = Remuneration::create([
        'medecin_id' => $medecin->id,
        'user_id' => $user->id,
        'periode_debut' => now()->startOfMonth()->toDateString(),
        'periode_fin' => now()->endOfMonth()->toDateString(),
        'type_remuneration' => 'pourcentage',
        'pourcentage' => 50,
        'montant_base' => 50000,
        'montant_medecin' => 25000,
        'montant_clinique' => 25000,
        'statut' => 'calculee',
    ]);

    $response = $this->get(route('remunerations.show', $remuneration));
    $response->assertStatus(200);
});
