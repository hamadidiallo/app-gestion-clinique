<?php

use App\Models\Acte;
use App\Models\ModePaiement;
use App\Models\Patient;
use App\Models\Recette;
use App\Models\Role;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('le tableau de bord ventile correctement le chiffre daffaires des services selon les encaissements du jour', function () {
    $roleAdmin = Role::firstOrCreate(['nom' => 'Administrateur']);
    $user = User::create([
        'nom' => 'Admin',
        'prenom' => 'Test',
        'email' => 'admin.test@clinique.com',
        'password' => bcrypt('password'),
        'role_id' => $roleAdmin->id,
    ]);

    $serviceMedGen = Service::create([
        'code' => 'MED-GEN',
        'nom' => 'Médecine Générale',
        'statut' => true,
    ]);

    $servicePed = Service::create([
        'code' => 'PED',
        'nom' => 'Pédiatrie',
        'statut' => true,
    ]);

    $acte = Acte::create([
        'service_id' => $serviceMedGen->id,
        'code' => 'ACT-001',
        'nom' => 'Consultation Médecine Générale',
        'tarif_normal' => 5000,
        'part_medecin_pourcentage' => 0,
        'part_clinique_pourcentage' => 100,
        'statut' => true,
    ]);

    $patient = Patient::create([
        'reference' => 'PAT-0001',
        'nom' => 'Diallo',
        'prenom' => 'Moussa',
        'telephone' => '70000000',
        'sexe' => 'M',
        'age' => 30,
    ]);

    $mode = ModePaiement::create([
        'code' => 'ESP',
        'nom' => 'Espèces',
        'statut' => true,
    ]);

    // Ticket émis hier
    $ticket = Ticket::create([
        'patient_id' => $patient->id,
        'service_id' => $serviceMedGen->id,
        'user_id' => $user->id,
        'reference' => 'TCK-20260917-001',
        'date_ticket' => Carbon::yesterday(),
        'montant_total' => 5000,
        'montant_patient' => 5000,
        'montant_paye' => 5000,
        'reste_a_payer' => 0,
        'statut' => 'paye',
    ]);

    TicketDetail::create([
        'ticket_id' => $ticket->id,
        'designation' => 'Consultation Médecine Générale',
        'type_item' => 'acte',
        'quantite' => 1,
        'prix_unitaire' => 5000,
        'montant_total' => 5000,
        'montant_patient' => 5000,
    ]);

    // Recette encaissée aujourd'hui
    Recette::create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'mode_paiement_id' => $mode->id,
        'montant' => 5000,
        'date_recette' => Carbon::today(),
        'reference' => 'REC-TODAY-01',
        'statut' => true,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard', ['periode' => 'jour']));

    $response->assertOk();
    $response->assertSee('5 000 FCFA');
    $response->assertSee('Médecine Générale');
    $response->assertSee('1 acte(s)');
});

test('le tableau de bord affiche correctement la facturation et les encaissements sur le filtre mensuel', function () {
    $roleAdmin = Role::firstOrCreate(['nom' => 'Administrateur']);
    $user = User::create([
        'nom' => 'Admin2',
        'prenom' => 'Test',
        'email' => 'admin2.test@clinique.com',
        'password' => bcrypt('password'),
        'role_id' => $roleAdmin->id,
    ]);

    $servicePed = Service::create([
        'code' => 'PED',
        'nom' => 'Pédiatrie',
        'statut' => true,
    ]);

    $patient = Patient::create([
        'reference' => 'PAT-0002',
        'nom' => 'Traoré',
        'prenom' => 'Aminata',
        'telephone' => '71000000',
        'sexe' => 'F',
        'age' => 5,
    ]);

    // Ticket émis ce mois-ci
    $ticket = Ticket::create([
        'patient_id' => $patient->id,
        'service_id' => $servicePed->id,
        'user_id' => $user->id,
        'reference' => 'TCK-20260918-002',
        'date_ticket' => Carbon::now(),
        'montant_total' => 15000,
        'montant_patient' => 15000,
        'montant_paye' => 0,
        'reste_a_payer' => 15000,
        'statut' => 'en_attente',
    ]);

    TicketDetail::create([
        'ticket_id' => $ticket->id,
        'designation' => 'Consultation Pédiatrique',
        'type_item' => 'acte',
        'quantite' => 1,
        'prix_unitaire' => 15000,
        'montant_total' => 15000,
        'montant_patient' => 15000,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard', ['periode' => 'mois']));

    $response->assertOk();
    $response->assertSee('15 000 FCFA');
    $response->assertSee('Pédiatrie');
});
