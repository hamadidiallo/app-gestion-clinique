<?php

use App\Models\Clinique;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Initialisation des rôles indispensables
    $this->adminRole = Role::firstOrCreate(
        ['nom' => 'Administrateur'],
        ['description' => 'Admin local clinique']
    );

    // Clinique A
    $this->cliniqueA = Clinique::create([
        'nom' => 'Clinique A (Gahambani)',
        'slug' => 'clinique-a',
        'code' => 'CLA',
        'ville' => 'Bamako',
        'pays' => 'Mali',
        'devise' => 'FCFA',
        'statut' => 'actif',
        'prefixe_ticket' => 'TCK',
        'prefixe_patient' => 'PAT',
    ]);

    // Clinique B
    $this->cliniqueB = Clinique::create([
        'nom' => 'Clinique B (Espoir)',
        'slug' => 'clinique-b',
        'code' => 'CLB',
        'ville' => 'Sikasso',
        'pays' => 'Mali',
        'devise' => 'FCFA',
        'statut' => 'actif',
        'prefixe_ticket' => 'TCK',
        'prefixe_patient' => 'PAT',
    ]);

    // Utilisateur Clinique A
    $this->userA = User::create([
        'clinique_id' => $this->cliniqueA->id,
        'nom' => 'Diallo',
        'prenom' => 'Amadou',
        'email' => 'amadou@clinique-a.ml',
        'password' => bcrypt('password'),
        'role_id' => $this->adminRole->id,
    ]);

    // Utilisateur Clinique B
    $this->userB = User::create([
        'clinique_id' => $this->cliniqueB->id,
        'nom' => 'Traoré',
        'prenom' => 'Bakary',
        'email' => 'bakary@clinique-b.ml',
        'password' => bcrypt('password'),
        'role_id' => $this->adminRole->id,
    ]);
});

test('un utilisateur de la clinique A ne peut voir que les patients de sa clinique', function () {
    // Création d'un patient pour Clinique A et un pour Clinique B
    $patientA = Patient::create([
        'clinique_id' => $this->cliniqueA->id,
        'nom' => 'Coulibaly',
        'prenom' => 'Fatoumata',
        'sexe' => 'F',
        'statut' => 'actif',
    ]);

    $patientB = Patient::create([
        'clinique_id' => $this->cliniqueB->id,
        'nom' => 'Sidibé',
        'prenom' => 'Ousmane',
        'sexe' => 'M',
        'statut' => 'actif',
    ]);

    // En tant qu'utilisateur de la Clinique A
    $this->actingAs($this->userA);

    // L'ORM ne doit voir que le patient A
    expect(Patient::count())->toBe(1)
        ->and(Patient::first()->id)->toBe($patientA->id);

    // La page index ne doit afficher que Fatoumata et pas Ousmane
    $response = $this->get(route('patients.index'));
    $response->assertStatus(200);
    $response->assertSee('Fatoumata');
    $response->assertDontSee('Sidibé');

    // L'accès direct au patient de la Clinique B doit échouer (404)
    $responseShow = $this->get(route('patients.show', $patientB));
    $responseShow->assertStatus(404);
});

test('la création d\'un patient assigne automatiquement le clinique_id de l\'utilisateur connecté', function () {
    $this->actingAs($this->userA);

    $nouveauPatient = Patient::create([
        'nom' => 'Konaté',
        'prenom' => 'Moussa',
        'sexe' => 'M',
        'statut' => 'actif',
    ]);

    expect($nouveauPatient->clinique_id)->toBe($this->cliniqueA->id);
});

test('l\'isolation des tickets et données financières est totale entre cliniques', function () {
    $patientA = Patient::create([
        'clinique_id' => $this->cliniqueA->id,
        'nom' => 'Diallo',
        'prenom' => 'Kadiatou',
        'sexe' => 'F',
        'statut' => 'actif',
    ]);

    $ticketA = Ticket::create([
        'clinique_id' => $this->cliniqueA->id,
        'patient_id' => $patientA->id,
        'user_id' => $this->userA->id,
        'reference' => 'TCK-CLA-001',
        'date_ticket' => now(),
        'montant_total' => 10000,
        'montant_patient' => 10000,
        'montant_assurance' => 0,
        'montant_paye' => 10000,
        'reste_a_payer' => 0,
        'statut' => 'regle',
    ]);

    // Utilisateur B ne doit rien voir du ticket A
    $this->actingAs($this->userB);
    expect(Ticket::count())->toBe(0);

    // Utilisateur A voit son ticket
    $this->actingAs($this->userA);
    expect(Ticket::count())->toBe(1)
        ->and(Ticket::first()->reference)->toBe('TCK-CLA-001');
});
