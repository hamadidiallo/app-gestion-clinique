<?php

use App\Models\Assurance;
use App\Models\Caisse;
use App\Models\Dette;
use App\Models\ModePaiement;
use App\Models\MouvementCaisse;
use App\Models\Paiement;
use App\Models\Patient;
use App\Models\Recette;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;

function getTestAdminUser(): User
{
    $role = Role::firstOrCreate(['nom' => 'Administrateur']);

    return User::firstOrCreate(
        ['email' => 'admin_test@clinique.local'],
        [
            'nom' => 'Admin',
            'prenom' => 'Test',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]
    );
}

test('can create a ticket with multi-items (acte, medicament, hospitalisation)', function () {
    $user = getTestAdminUser();
    $assurance = Assurance::firstOrCreate(
        ['nom' => 'INPS Mali'],
        ['code' => 'INPS', 'taux_par_defaut' => 80, 'statut' => true]
    );

    $patient = Patient::create([
        'nom' => 'Diallo',
        'prenom' => 'Amadou',
        'sexe' => 'M',
        'telephone' => '70112233',
        'statut' => 'assure',
        'assurance_id' => $assurance->id,
        'numero_assure' => 'INPS-123456',
        'taux_couverture' => 80,
    ]);

    $postData = [
        'patient_id' => $patient->id,
        'assurance_id' => $assurance->id,
        'taux_assurance' => 80,
        'date_ticket' => now()->format('Y-m-d H:i:s'),
        'montant_total' => 31000,
        'montant_assurance' => 24800,
        'montant_patient' => 6200,
        'montant_paye' => 5000,
        'statut' => 'partiellement_paye',
        'description' => 'Facturation multiple clinique',
        'items' => [
            [
                'type_item' => 'acte',
                'designation' => 'Consultation Spécialiste Cardiologie',
                'quantite' => 1,
                'prix_unitaire' => 10000,
                'montant_total' => 10000,
            ],
            [
                'type_item' => 'medicament',
                'designation' => 'Amoxicilline 1g Comprimés',
                'quantite' => 2,
                'prix_unitaire' => 3000,
                'montant_total' => 6000,
            ],
            [
                'type_item' => 'hospitalisation',
                'designation' => 'Chambre Hospitalisation Médecine (1 Nuit)',
                'quantite' => 1,
                'prix_unitaire' => 15000,
                'montant_total' => 15000,
            ],
        ],
    ];

    $response = $this->actingAs($user)->post(route('tickets.store'), $postData);

    $response->assertRedirect();

    // Vérifier création ticket
    $ticket = Ticket::where('patient_id', $patient->id)->latest()->first();
    expect($ticket)->not->toBeNull()
        ->and((float) $ticket->montant_total)->toBe(31000.0)
        ->and((float) $ticket->montant_assurance)->toBe(24800.0)
        ->and((float) $ticket->montant_patient)->toBe(6200.0)
        ->and((float) $ticket->reste_a_payer)->toBe(1200.0);

    // Vérifier création des 3 détails
    expect($ticket->details)->toHaveCount(3);

    $acte = $ticket->details->firstWhere('type_item', 'acte');
    expect($acte)->not->toBeNull()
        ->and($acte->designation)->toBe('Consultation Spécialiste Cardiologie');

    $med = $ticket->details->firstWhere('type_item', 'medicament');
    expect($med)->not->toBeNull()
        ->and($med->designation)->toBe('Amoxicilline 1g Comprimés')
        ->and((float) $med->quantite)->toBe(2.0);

    $hosp = $ticket->details->firstWhere('type_item', 'hospitalisation');
    expect($hosp)->not->toBeNull()
        ->and($hosp->designation)->toBe('Chambre Hospitalisation Médecine (1 Nuit)')
        ->and((float) $hosp->montant_total)->toBe(15000.0);

    // Vérifier création automatique de la dette
    $dette = Dette::where('ticket_id', $ticket->id)->first();
    expect($dette)->not->toBeNull()
        ->and((float) $dette->reste_a_payer)->toBe(1200.0);
});

test('patient show page displays medical history populated by ticket items', function () {
    $user = getTestAdminUser();
    $patient = Patient::create([
        'nom' => 'Kone',
        'prenom' => 'Fatoumata',
        'sexe' => 'F',
        'telephone' => '65443322',
        'statut' => 'non_assure',
    ]);

    $ticket = Ticket::create([
        'patient_id' => $patient->id,
        'user_id' => $user->id,
        'reference' => 'TCK-TEST-001',
        'date_ticket' => now(),
        'montant_total' => 8000,
        'montant_assurance' => 0,
        'montant_patient' => 8000,
        'montant_paye' => 8000,
        'reste_a_payer' => 0,
        'statut' => 'paye',
    ]);

    TicketDetail::create([
        'ticket_id' => $ticket->id,
        'designation' => 'Échographie Pelvienne',
        'type_item' => 'acte',
        'quantite' => 1,
        'prix_unitaire' => 8000,
        'montant_total' => 8000,
        'taux_couverture' => 0,
        'montant_assurance' => 0,
        'montant_patient' => 8000,
    ]);

    $response = $this->actingAs($user)->get(route('patients.show', $patient));

    $response->assertStatus(200);
    $response->assertSee('Échographie Pelvienne');
    $response->assertSee('Acte Médical');
    $response->assertSee('8 000 FCFA');
});

test('can update permanent medical record of a patient', function () {
    $user = getTestAdminUser();
    $patient = Patient::create([
        'nom' => 'Keita',
        'prenom' => 'Ibrahim',
        'sexe' => 'M',
        'telephone' => '78998877',
        'statut' => 'non_assure',
    ]);

    $postData = [
        'groupe_sanguin' => 'O+',
        'allergies' => 'Pénicilline et Bétadine',
        'antecedents_personnels' => 'Asthme bronchique depuis lenfance',
        'antecedents_chirurgicaux' => 'Herniorraphie inguinale 2019',
        'antecedents_familiaux' => 'Diabète maternel',
        'notes_particulieres' => 'Patient anxieux',
    ];

    $response = $this->actingAs($user)->post(route('patients.dossier-medical.update', $patient), $postData);

    $response->assertRedirect();

    $this->assertDatabaseHas('dossiers_medicaux', [
        'patient_id' => $patient->id,
        'groupe_sanguin' => 'O+',
        'allergies' => 'Pénicilline et Bétadine',
        'antecedents_personnels' => 'Asthme bronchique depuis lenfance',
    ]);
});

test('ticket creation with an open caisse records paiement, recette and mouvement_caisse correctly', function () {
    $user = getTestAdminUser();
    $mode = ModePaiement::firstOrCreate(['nom' => 'Espèces'], ['code' => 'ESP', 'statut' => true]);
    $caisse = Caisse::create([
        'user_id' => $user->id,
        'date_ouverture' => now(),
        'fonds_initial' => 50000,
        'total_entrees' => 0,
        'total_sorties' => 0,
        'solde_theorique' => 50000,
        'statut' => 'ouverte',
    ]);

    $patient = Patient::create([
        'nom' => 'Traoré',
        'prenom' => 'Moussa',
        'sexe' => 'M',
        'telephone' => '76112233',
        'statut' => 'non_assure',
    ]);

    $postData = [
        'patient_id' => $patient->id,
        'date_ticket' => now()->format('Y-m-d H:i:s'),
        'montant_total' => 15000,
        'montant_assurance' => 0,
        'montant_patient' => 15000,
        'montant_paye' => 10000,
        'statut' => 'partiellement_paye',
        'mode_paiement_id' => $mode->id,
        'items' => [
            [
                'type_item' => 'acte',
                'designation' => 'Consultation Généraliste',
                'quantite' => 1,
                'prix_unitaire' => 15000,
                'montant_total' => 15000,
            ],
        ],
    ];

    $response = $this->actingAs($user)->post(route('tickets.store'), $postData);
    $response->assertRedirect();

    $ticket = Ticket::where('patient_id', $patient->id)->latest()->first();
    expect($ticket)->not->toBeNull()
        ->and((float) $ticket->montant_paye)->toBe(10000.0)
        ->and((float) $ticket->reste_a_payer)->toBe(5000.0);

    // Vérifier création du Paiement
    $paiement = Paiement::where('ticket_id', $ticket->id)->first();
    expect($paiement)->not->toBeNull()
        ->and((float) $paiement->montant_impute)->toBe(10000.0);

    // Vérifier création de la Recette
    $recette = Recette::where('ticket_id', $ticket->id)->first();
    expect($recette)->not->toBeNull()
        ->and($recette->paiement_id)->toBe($paiement->id)
        ->and((float) $recette->montant)->toBe(10000.0)
        ->and($recette->mode_paiement_id)->toBe($mode->id);

    // Vérifier création du MouvementCaisse
    $mouvement = MouvementCaisse::where('caisse_id', $caisse->id)->latest()->first();
    expect($mouvement)->not->toBeNull()
        ->and($mouvement->type)->toBe('entree')
        ->and($mouvement->origine)->toBe('paiement')
        ->and((float) $mouvement->montant)->toBe(10000.0);

    // Vérifier incrément du solde de caisse
    $caisse->refresh();
    expect((float) $caisse->solde_theorique)->toBe(60000.0)
        ->and((float) $caisse->total_entrees)->toBe(10000.0);
});

test('ticket index search does not crash with unknown column', function () {
    $user = getTestAdminUser();
    $patient = Patient::create([
        'nom' => 'Diarra',
        'prenom' => 'Souleymane',
        'sexe' => 'M',
        'telephone' => '71223344',
        'numero_assure' => 'AMO-998877',
        'statut' => 'assure',
    ]);

    Ticket::create([
        'patient_id' => $patient->id,
        'user_id' => $user->id,
        'reference' => 'TCK-DIARRA-001',
        'date_ticket' => now(),
        'montant_total' => 5000,
        'montant_assurance' => 4000,
        'montant_patient' => 1000,
        'montant_paye' => 1000,
        'reste_a_payer' => 0,
        'statut' => 'paye',
    ]);

    // Test de recherche par mot-clé
    $response = $this->actingAs($user)->get(route('tickets.index', ['q' => 'Diarra']));
    $response->assertStatus(200);
    $response->assertSee('TCK-DIARRA-001');
});

test('patient create and edit interfaces render successfully with new design system', function () {
    $user = getTestAdminUser();
    $patient = Patient::create([
        'nom' => 'Coulibaly',
        'prenom' => 'Ousmane',
        'sexe' => 'M',
        'telephone' => '72001122',
        'statut' => 'non_assure',
    ]);

    $resCreate = $this->actingAs($user)->get(route('patients.create'));
    $resCreate->assertStatus(200);
    $resCreate->assertSee('Admission');
    $resCreate->assertSee('Clinique Gahambani');

    $resEdit = $this->actingAs($user)->get(route('patients.edit', $patient));
    $resEdit->assertStatus(200);
    $resEdit->assertSee('Modifier Dossier : Ousmane Coulibaly');
});
