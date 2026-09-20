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

test('le caissier a un tableau de bord dedie guichet et ne voit pas le chiffre daffaires des services ni les charges ni le solde net', function () {
    $roleCaissier = Role::firstOrCreate(['nom' => 'Caissier']);
    $caissier = User::create([
        'nom' => 'Kone',
        'prenom' => 'Fatou',
        'email' => 'fatou.caissiere@clinique.com',
        'password' => bcrypt('password'),
        'role_id' => $roleCaissier->id,
    ]);

    $service = Service::create([
        'code' => 'GYN',
        'nom' => 'Gynécologie',
        'statut' => true,
    ]);

    $patient = Patient::create([
        'reference' => 'PAT-0099',
        'nom' => 'Ouattara',
        'prenom' => 'Awa',
        'telephone' => '72000000',
        'sexe' => 'F',
        'age' => 28,
    ]);

    $ticket = Ticket::create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'user_id' => $caissier->id,
        'reference' => 'TCK-20260919-CAISSE-01',
        'date_ticket' => Carbon::today(),
        'montant_total' => 8000,
        'montant_patient' => 8000,
        'montant_paye' => 8000,
        'reste_a_payer' => 0,
        'statut' => 'paye',
    ]);

    $response = $this->actingAs($caissier)->get(route('dashboard'));

    $response->assertOk();
    // Données visibles par le caissier
    $response->assertSee('Tableau de Bord Guichet');
    $response->assertSee('Mes Encaissements');
    $response->assertSee('Mes Tickets Émis');
    $response->assertSee('Actions Rapides du Guichet');
    $response->assertSee('Mes Derniers Tickets Émis');
    $response->assertSee('TCK-20260919-CAISSE-01');

    // Données confidentielles STRICTEMENT masquées pour le caissier
    $response->assertDontSee("Chiffre d'Affaires par Service Médical", false);
    $response->assertDontSee("Solde d'exploitation net", false);
    $response->assertDontSee('Honoraires Praticiens');
    $response->assertDontSee('Dépenses & charges');
});

test('le comptable a acces a la vue financiere globale et au chiffre daffaires par service', function () {
    $roleComptable = Role::firstOrCreate(['nom' => 'Comptable']);
    $comptable = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Compta',
        'email' => 'comptable@clinique.com',
        'password' => bcrypt('password'),
        'role_id' => $roleComptable->id,
    ]);

    $response = $this->actingAs($comptable)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee("Chiffre d'Affaires par Service Médical", false);
    $response->assertSee("Solde d'exploitation net", false);
    $response->assertSee('Charges');
    $response->assertSee('Honoraires Praticiens');
});
