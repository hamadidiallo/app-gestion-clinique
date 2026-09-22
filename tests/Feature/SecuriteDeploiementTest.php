<?php

use App\Models\Clinique;
use App\Models\DossierMedical;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Non-régressions issues de l'audit de pré-déploiement.
 * Chaque test correspond à une faille qui avait été démontrée puis corrigée.
 * Les fabriques creerClinique() et creerUtilisateur() viennent de tests/Pest.php.
 */
test("la route d'execution des migrations n'existe plus", function () {
    $this->get('/run-migrations')->assertNotFound();
});

test('les lignes de ticket sont cloisonnees par clinique', function () {
    $role = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');
    $b = creerClinique('Clinique B', 'CLB');

    $userA = creerUtilisateur($a, 'a@a.ml', $role);
    $userB = creerUtilisateur($b, 'b@b.ml', $role);

    $patientA = Patient::create([
        'clinique_id' => $a->id, 'nom' => 'SecretA', 'prenom' => 'Confidentiel',
        'sexe' => 'F', 'statut' => 'actif',
    ]);

    $ticketA = Ticket::create([
        'clinique_id' => $a->id, 'patient_id' => $patientA->id, 'user_id' => $userA->id,
        'reference' => 'TCK-A-001', 'date_ticket' => now(), 'montant_total' => 10000,
        'montant_patient' => 10000, 'montant_assurance' => 0, 'montant_paye' => 0,
        'reste_a_payer' => 10000, 'statut' => 'impaye',
    ]);

    $ligneA = TicketDetail::create([
        'clinique_id' => $a->id, 'ticket_id' => $ticketA->id, 'designation' => 'ACTE-SECRET-A',
        'quantite' => 1, 'prix_unitaire' => 10000, 'montant_total' => 10000,
        'montant_assurance' => 0, 'montant_patient' => 10000,
    ]);

    // La clinique B ne voit ni la ligne via l'ORM, ni via la page, ni en accès direct
    $this->actingAs($userB);
    expect(TicketDetail::count())->toBe(0);
    $this->get(route('ticketdetails.index'))->assertOk()->assertDontSee('ACTE-SECRET-A');
    $this->get(route('ticketdetails.show', $ligneA))->assertNotFound();
    $this->delete(route('ticketdetails.destroy', $ligneA))->assertNotFound();

    // La clinique A retrouve bien sa propre ligne
    $this->actingAs($userA);
    expect(TicketDetail::count())->toBe(1);
});

test("le role n'est jamais accepte depuis le formulaire d'inscription", function () {
    $receptionniste = Role::firstOrCreate(['nom' => 'Réceptionniste'], ['description' => 'x']);
    $adminRole = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');

    $this->post('/register', [
        'action_type' => 'rejoindre',
        'code_invitation' => $a->code_invitation,
        'nom' => 'Pirate', 'prenom' => 'Mr', 'email' => 'pirate@x.ml',
        'telephone' => '76 44 55 66',
        'password' => 'motdepasse', 'password_confirmation' => 'motdepasse',
        'role_id' => $adminRole->id, // tentative d'escalade
    ]);

    $pirate = User::where('email', 'pirate@x.ml')->first();
    expect($pirate)->not->toBeNull()
        ->and($pirate->role_id)->toBe($receptionniste->id)
        ->and($pirate->role_id)->not->toBe($adminRole->id);
});

test('une clinique suspendue est redirigee vers la page abonnement', function () {
    $role = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');
    $a->update(['statut' => 'suspendu']);

    $user = creerUtilisateur($a, 'a@a.ml', $role);

    $this->actingAs($user)
        ->get(route('patients.index'))
        ->assertRedirect(route('abonnement.suspendu'));
});

test('les tentatives de connexion repetees sont bloquees', function () {
    $reponse = null;

    for ($i = 0; $i < 10; $i++) {
        $reponse = $this->post('/login', ['email' => 'x@x.ml', 'password' => 'faux'.$i]);
    }

    expect($reponse->status())->toBe(429);
});

test('un administrateur ne peut pas gerer les comptes des autres cliniques', function () {
    $role = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');
    $b = creerClinique('Clinique B', 'CLB');

    $adminA = creerUtilisateur($a, 'admin@a.ml', $role);
    $userB = creerUtilisateur($b, 'victime@b.ml', $role);

    $this->actingAs($adminA);

    $this->get(route('users.index'))->assertOk()->assertDontSee('victime@b.ml');
    $this->get(route('users.show', $userB))->assertNotFound();
    $this->get(route('users.edit', $userB))->assertNotFound();
    $this->delete(route('users.destroy', $userB))->assertNotFound();

    expect(User::find($userB->id))->not->toBeNull();
});

test('un compte cree par un administrateur est rattache a sa clinique', function () {
    $role = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);
    $caissier = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');
    $adminA = creerUtilisateur($a, 'admin@a.ml', $role);

    $this->actingAs($adminA)->post(route('users.store'), [
        'nom' => 'Nouveau', 'prenom' => 'Agent', 'email' => 'agent@a.ml',
        'telephone' => '76 11 22 33',
        'password' => 'motdepasse', 'password_confirmation' => 'motdepasse',
        'role_id' => $caissier->id,
    ]);

    $agent = User::where('email', 'agent@a.ml')->first();
    expect($agent)->not->toBeNull()
        ->and($agent->clinique_id)->toBe($a->id);
});

test('la connexion accepte le telephone sous toutes ses ecritures', function () {
    $role = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');

    $user = User::create([
        'clinique_id' => $a->id, 'nom' => 'Kone', 'prenom' => 'Dr',
        'email' => 'dr.kone@clinique.ml', 'telephone' => '76 00 00 00',
        'password' => bcrypt('password123'), 'role_id' => $role->id,
    ]);

    // Le numero est stocke sous forme canonique
    expect($user->fresh()->telephone)->toBe('76000000');

    // Toutes ces ecritures doivent mener au meme compte
    foreach (['76000000', '76 00 00 00', '+223 76 00 00 00', '00223 76-00-00-00'] as $saisie) {
        auth()->logout();

        $this->post('/login', ['email' => $saisie, 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));

        expect(auth()->id())->toBe($user->id);
    }
});

test('la connexion par email reste possible', function () {
    $role = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');

    $user = User::create([
        'clinique_id' => $a->id, 'nom' => 'Kone', 'prenom' => 'Dr',
        'email' => 'dr.kone@clinique.ml', 'telephone' => '76000000',
        'password' => bcrypt('password123'), 'role_id' => $role->id,
    ]);

    $this->post('/login', ['email' => 'dr.kone@clinique.ml', 'password' => 'password123'])
        ->assertRedirect(route('dashboard'));

    expect(auth()->id())->toBe($user->id);
});

test('un identifiant partiel ne donne plus acces a un compte', function () {
    $role = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');

    User::create([
        'clinique_id' => $a->id, 'nom' => 'Kone', 'prenom' => 'Dr',
        'email' => 'dr.kone@clinique.ml', 'telephone' => '76000000',
        'password' => bcrypt('password123'), 'role_id' => $role->id,
    ]);

    // L'ancien repli acceptait « dr.kone » comme prefixe de l'email : ce n'est plus le cas
    $this->post('/login', ['email' => 'dr.kone', 'password' => 'password123']);

    expect(auth()->check())->toBeFalse();
});

test('un compte sans telephone ne peut pas etre atteint par une saisie vide', function () {
    $role = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');

    User::create([
        'clinique_id' => $a->id, 'nom' => 'Ancien', 'prenom' => 'Compte',
        'email' => 'ancien@clinique.ml', 'telephone' => null,
        'password' => bcrypt('password123'), 'role_id' => $role->id,
    ]);

    // Une saisie sans aucun chiffre se normalise en null : elle ne doit correspondre a personne
    $this->post('/login', ['email' => '----', 'password' => 'password123']);

    expect(auth()->check())->toBeFalse();
});

test('le code invitation resiste a l enumeration', function () {
    $codes = collect(range(1, 40))->map(fn () => Clinique::genererCodeInvitation('Clinique du Fleuve'));

    // Format dictable : prefixe court + 6 caracteres non ambigus
    $codes->each(function (string $code) {
        expect($code)->toMatch('/^CLIN-[ABCDEFGHJKLMNPQRSTUVWXYZ23456789]{6}$/');
    });

    // Aucune collision et une entropie reelle (plus de 4 chiffres previsibles)
    expect($codes->unique()->count())->toBe(40);
});

test('un compte peut etre cree avec le seul telephone ou le seul email', function () {
    $role = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);
    $caissier = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');
    $adminA = creerUtilisateur($a, 'admin@a.ml', $role);

    $this->actingAs($adminA);

    // Uniquement le telephone
    $this->post(route('users.store'), [
        'nom' => 'Sans', 'prenom' => 'Email', 'telephone' => '76 11 11 11',
        'password' => 'motdepasse', 'password_confirmation' => 'motdepasse',
        'role_id' => $caissier->id,
    ])->assertSessionHasNoErrors();

    $sansEmail = User::where('telephone', '76111111')->first();
    expect($sansEmail)->not->toBeNull()
        ->and($sansEmail->email)->toBeNull();

    // Uniquement l email
    $this->post(route('users.store'), [
        'nom' => 'Sans', 'prenom' => 'Telephone', 'email' => 'sans.tel@a.ml',
        'password' => 'motdepasse', 'password_confirmation' => 'motdepasse',
        'role_id' => $caissier->id,
    ])->assertSessionHasNoErrors();

    $sansTel = User::where('email', 'sans.tel@a.ml')->first();
    expect($sansTel)->not->toBeNull()
        ->and($sansTel->telephone)->toBeNull();
});

test('un compte sans aucun identifiant est refuse', function () {
    $role = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);
    $caissier = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');

    $this->actingAs(creerUtilisateur($a, 'admin@a.ml', $role));

    $this->from(route('user.create'))->post(route('users.store'), [
        'nom' => 'Aucun', 'prenom' => 'Identifiant',
        'password' => 'motdepasse', 'password_confirmation' => 'motdepasse',
        'role_id' => $caissier->id,
    ])->assertSessionHasErrors(['email', 'telephone']);

    expect(User::where('nom', 'Aucun')->exists())->toBeFalse();
});

test('chacun des deux identifiants permet de se connecter', function () {
    $role = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);
    $a = creerClinique('Clinique A', 'CLA');

    $parTelephone = User::create([
        'clinique_id' => $a->id, 'nom' => 'Tel', 'prenom' => 'Seul',
        'telephone' => '76 22 22 22', 'password' => bcrypt('password123'), 'role_id' => $role->id,
    ]);

    $parEmail = User::create([
        'clinique_id' => $a->id, 'nom' => 'Mail', 'prenom' => 'Seul',
        'email' => 'mail.seul@a.ml', 'password' => bcrypt('password123'), 'role_id' => $role->id,
    ]);

    auth()->logout();
    $this->post('/login', ['email' => '76 22 22 22', 'password' => 'password123']);
    expect(auth()->id())->toBe($parTelephone->id);

    auth()->logout();
    $this->post('/login', ['email' => 'mail.seul@a.ml', 'password' => 'password123']);
    expect(auth()->id())->toBe($parEmail->id);
});

test('le secret medical ne fuit pas par l autocompletion patient', function () {
    $clinique = creerClinique('Clinique A', 'CLA');
    $medecinRole = Role::firstOrCreate(['nom' => 'Médecin'], ['description' => 'x']);
    $caissierRole = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);

    $patient = Patient::create([
        'clinique_id' => $clinique->id, 'nom' => 'Traore', 'prenom' => 'Awa',
        'sexe' => 'F', 'statut' => 'actif',
    ]);
    DossierMedical::create([
        'patient_id' => $patient->id,
        'numero_dossier' => 'DOS-SEC-1',
        'groupe_sanguin' => 'O+',
        'allergies' => 'ALLERGIE-CONFIDENTIELLE',
        'antecedents_personnels' => 'ANTECEDENT-CONFIDENTIEL',
    ]);

    // Le medecin a besoin de ces informations pour la consultation
    $this->actingAs(creerUtilisateur($clinique, 'medecin@a.ml', $medecinRole));
    $reponseMedecin = $this->getJson(route('patients.search', ['q' => 'Traore']));
    $reponseMedecin->assertOk();
    expect($reponseMedecin->json('0.allergies'))->toBe('ALLERGIE-CONFIDENTIELLE');

    // Le caissier utilise la meme autocompletion pour les tickets : il ne doit
    // pas recevoir le dossier medical, meme sans l afficher
    $this->actingAs(creerUtilisateur($clinique, 'caissier@a.ml', $caissierRole));
    $reponseCaissier = $this->getJson(route('patients.search', ['q' => 'Traore']));
    $reponseCaissier->assertOk()
        ->assertDontSee('ALLERGIE-CONFIDENTIELLE')
        ->assertDontSee('ANTECEDENT-CONFIDENTIEL');

    // Il conserve en revanche ce dont il a besoin pour facturer
    expect($reponseCaissier->json('0.nom_complet'))->toBe('Awa Traore');
});
