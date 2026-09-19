<?php

use App\Models\Acte;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Prestation;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('la page du rapport medecins affiche les honoraires, kpis et devises FCFA', function () {
    $roleAdmin = Role::firstOrCreate(['nom' => 'Administrateur']);
    $user = User::create([
        'nom' => 'Admin',
        'prenom' => 'Directeur',
        'email' => 'admin_medecin@clinique.com',
        'password' => bcrypt('password'),
        'role_id' => $roleAdmin->id,
    ]);

    $medecin = Medecin::create([
        'code' => 'DR-TEST-001',
        'nom' => 'DIALLO',
        'prenom' => 'Mahamadou',
        'specialite' => 'Médecine Générale',
        'pourcentage' => 40,
        'statut' => true,
    ]);

    $patient = Patient::create([
        'reference' => 'PAT-2026-0200',
        'nom' => 'Santara',
        'prenom' => 'Mahamadou',
        'telephone' => '76000002',
        'sexe' => 'M',
    ]);

    $service = Service::create([
        'nom' => 'Médecine Générale',
        'code' => 'MED-GEN',
        'description' => 'Service de médecine générale',
    ]);

    $acte = Acte::create([
        'service_id' => $service->id,
        'nom' => 'Consultation Médecine Générale',
        'code' => 'CONS-MG-01',
        'tarif' => 5000,
        'statut' => true,
    ]);

    Prestation::create([
        'reference' => 'SOIN-20260918-0001',
        'patient_id' => $patient->id,
        'medecin_id' => $medecin->id,
        'service_id' => $service->id,
        'acte_id' => $acte->id,
        'type' => 'Consultation Médecine Générale',
        'montant' => 5000,
        'pourcentage_medecin' => 40,
        'pourcentage_clinique' => 60,
        'part_medecin' => 2000,
        'part_clinique' => 3000,
        'date_prestation' => Carbon::now(),
        'statut' => true,
    ]);

    $response = $this->actingAs($user)->get(route('rapports.medecins'));

    $response->assertOk();
    $response->assertSee('Rétrocession');
    $response->assertSee('Honoraires des Médecins');
    $response->assertSee('Dr. DIALLO Mahamadou');
    $response->assertSee('2 000 FCFA');
    $response->assertSee('3 000 FCFA');
    $response->assertSee('5 000 FCFA');
    $response->assertSee('Générer le Bordereau');
    $response->assertDontSee('FBU');
});

test('la page du bordereau officiel honoraires medecins genere le document A4 avec montant en lettres et signatures', function () {
    $roleAdmin = Role::firstOrCreate(['nom' => 'Administrateur']);
    $user = User::create([
        'nom' => 'Admin',
        'prenom' => 'Directeur',
        'email' => 'admin_medecin_bord@clinique.com',
        'password' => bcrypt('password'),
        'role_id' => $roleAdmin->id,
    ]);

    $medecin = Medecin::create([
        'code' => 'DR-DIAL-001',
        'nom' => 'DIALLO',
        'prenom' => 'Mahamadou',
        'specialite' => 'Médecine Générale',
        'pourcentage' => 40,
        'statut' => true,
    ]);

    $patient = Patient::create([
        'reference' => 'PAT-2026-0201',
        'nom' => 'Traoré',
        'prenom' => 'Fatoumata',
        'telephone' => '76000003',
        'sexe' => 'F',
    ]);

    $service = Service::create([
        'nom' => 'Médecine Générale',
        'code' => 'MED-GEN-2',
        'description' => 'Service de consultation',
    ]);

    Prestation::create([
        'reference' => 'SOIN-20260918-0002',
        'patient_id' => $patient->id,
        'medecin_id' => $medecin->id,
        'service_id' => $service->id,
        'type' => 'Consultation Médicale',
        'montant' => 15000,
        'pourcentage_medecin' => 40,
        'pourcentage_clinique' => 60,
        'part_medecin' => 6000,
        'part_clinique' => 9000,
        'date_prestation' => Carbon::now(),
        'statut' => true,
    ]);

    // Test avec filtre par code médecin
    $response = $this->actingAs($user)->get(route('rapports.medecins.bordereau', [
        'medecin_id' => 'DR-DIAL-001',
    ]));

    $response->assertOk();
    $response->assertSee('Bordereau de Liquidation');
    $response->assertSee('BORD-HON-');
    $response->assertSee('Dr. DIALLO Mahamadou');
    $response->assertSee('DR-DIAL-001');
    $response->assertSee('6 000');
    $response->assertSee('Six mille Francs CFA');
    $response->assertSee('Service Comptabilité');
    $response->assertSee('Direction Médicale');
    $response->assertSee('Le Médecin Bénéficiaire');
});
