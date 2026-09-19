<?php

use App\Models\Assurance;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('la page du releve tiers payant assurances affiche les donnees et les totaux en FCFA', function () {
    $roleAdmin = Role::firstOrCreate(['nom' => 'Administrateur']);
    $user = User::create([
        'nom' => 'Admin',
        'prenom' => 'Directeur',
        'email' => 'directeur@clinique.com',
        'password' => bcrypt('password'),
        'role_id' => $roleAdmin->id,
    ]);

    $assurance = Assurance::create([
        'nom' => 'CANAM Mali',
        'code' => 'CANAM',
        'taux_par_defaut' => 80,
        'statut' => true,
    ]);

    $patient = Patient::create([
        'reference' => 'PAT-2026-0099',
        'nom' => 'Coulibaly',
        'prenom' => 'Oumar',
        'telephone' => '76000000',
        'sexe' => 'M',
        'age' => 42,
    ]);

    $ticket = Ticket::create([
        'patient_id' => $patient->id,
        'assurance_id' => $assurance->id,
        'user_id' => $user->id,
        'reference' => 'TCK-2026-ASSUR-01',
        'date_ticket' => Carbon::now(),
        'montant_total' => 10000,
        'montant_assurance' => 8000,
        'montant_patient' => 2000,
        'montant_paye' => 2000,
        'reste_a_payer' => 0,
        'statut' => 'paye',
    ]);

    TicketDetail::create([
        'ticket_id' => $ticket->id,
        'designation' => 'Consultation Médecine Spécialisée',
        'type_item' => 'acte',
        'quantite' => 1,
        'prix_unitaire' => 10000,
        'montant_total' => 10000,
        'taux_couverture' => 80,
        'montant_assurance' => 8000,
        'montant_patient' => 2000,
    ]);

    $response = $this->actingAs($user)->get(route('rapports.assurances'));

    $response->assertOk();
    $response->assertSee('Relevé Détaillé des Prestations Prises en Charge');
    $response->assertSee('CANAM Mali');
    $response->assertSee('Oumar Coulibaly');
    $response->assertSee('10 000');
    $response->assertSee('8 000');
    $response->assertSee('2 000');
    $response->assertSee('FCFA');
    $response->assertDontSee('FBU');
});

test('la page du bordereau officiel assurance genere le document A4 avec montant en lettres et signatures', function () {
    $roleAdmin = Role::firstOrCreate(['nom' => 'Administrateur']);
    $user = User::create([
        'nom' => 'Admin',
        'prenom' => 'Directeur',
        'email' => 'admin_bordereau@clinique.com',
        'password' => bcrypt('password'),
        'role_id' => $roleAdmin->id,
    ]);

    $assurance = Assurance::create([
        'nom' => 'CANAM / AMO',
        'code' => 'AMO',
        'taux_par_defaut' => 80,
        'statut' => true,
    ]);

    $patient = Patient::create([
        'reference' => 'PAT-2026-0100',
        'nom' => 'Traoré',
        'prenom' => 'Fatoumata',
        'telephone' => '76000001',
        'sexe' => 'F',
        'numero_assure' => 'AMO-998877-BKO',
        'taux_couverture' => 80,
    ]);

    $ticket = Ticket::create([
        'patient_id' => $patient->id,
        'assurance_id' => $assurance->id,
        'user_id' => $user->id,
        'reference' => 'TCK-2026-AMO-01',
        'date_ticket' => Carbon::now(),
        'montant_total' => 20000,
        'montant_assurance' => 16000,
        'montant_patient' => 4000,
        'montant_paye' => 4000,
        'reste_a_payer' => 0,
        'statut' => 'paye',
    ]);

    TicketDetail::create([
        'ticket_id' => $ticket->id,
        'designation' => 'Échographie Pelvienne',
        'type_item' => 'acte',
        'quantite' => 1,
        'prix_unitaire' => 20000,
        'montant_total' => 20000,
        'taux_couverture' => 80,
        'montant_assurance' => 16000,
        'montant_patient' => 4000,
    ]);

    // Test avec filtre sur le code assurance
    $response = $this->actingAs($user)->get(route('rapports.assurances.bordereau', [
        'assurance_id' => 'AMO',
    ]));

    $response->assertOk();
    $response->assertSee('Bordereau de Transmission');
    $response->assertSee('BORD-AMO-');
    $response->assertSee('CANAM / AMO');
    $response->assertSee('AMO-998877-BKO');
    $response->assertSee('16 000');
    $response->assertSee('Seize mille Francs CFA');
    $response->assertSee('Service Facturation');
    $response->assertSee('Direction Médicale');
    $response->assertSee('Réception Organisme Assureur');
});
