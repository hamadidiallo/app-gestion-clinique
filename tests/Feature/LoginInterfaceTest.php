<?php

use App\Models\Administration\Role;
use App\Models\Administration\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Auth::logout();
});

test('la nouvelle interface de connexion medigest saffiche correctement', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('MediGest');
    $response->assertSee('Connexion');
    $response->assertSee('Bon retour parmi nous.');
    $response->assertSee('Téléphone ou e-mail');
    $response->assertSee('Mot de passe');
    $response->assertSee('Mot de passe oublié ?');
    $response->assertSee('Rester connecté sur ce poste');
    $response->assertSee('Se connecter');
    // L'aide s'affiche dans la page, sans fenêtre alert() bloquante
    $response->assertSee('id="aide_mot_de_passe"', false);
    $response->assertDontSee('alert(', false);

    // Panneau : exemple de clôture de caisse
    $response->assertSee('Exemple de clôture de caisse');
    $response->assertSee('TOTAL ENCAISSÉ');
    // Promesses non tenues par l'application : ne pas les afficher
    $response->assertDontSee('données de santé chiffrées');
    $response->assertDontSee('Rendez-vous');
    $response->assertDontSee('pharmacie');
});

test('aucune donnee fictive de demonstration sur l ecran de connexion', function () {
    $response = $this->get(route('login'));

    // Aucune identité de patient ou de clinique inventée ne doit être affichée
    $response->assertDontSee('Awa Koné');
    $response->assertDontSee('Marché Dabanani');

    // Les champs d'inscription ne doivent pas être pré-remplis
    $response->assertDontSee('value="Clinique Baobab"', false);
    $response->assertDontSee('value="Bamako"', false);
    $response->assertDontSee('value="+223 20 22 44 66"', false);
});

test('un utilisateur peut se connecter via la nouvelle interface', function () {
    $role = Role::firstOrCreate(['nom' => 'Caissier']);
    $user = User::create([
        'nom' => 'Kone',
        'prenom' => 'Dr',
        'email' => 'dr.kone@clinique.com',
        'password' => bcrypt('password123'),
        'role_id' => $role->id,
    ]);

    $response = $this->post(route('login'), [
        'email' => 'dr.kone@clinique.com',
        'password' => 'password123',
        'remember' => '1',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('les ecrans de processus M0 M0b M0c et M0d sont presents et coherents', function () {
    $response = $this->get(route('register'));

    $response->assertOk();

    // M0 Bienvenue
    $response->assertSee('Bienvenue sur MediGest');
    $response->assertSee('Comment souhaitez-vous démarrer ?');
    $response->assertSee('Créer un établissement');
    $response->assertSee('Rejoindre une clinique');
    $response->assertSee('Une plateforme, deux façons de démarrer.');
    $response->assertSee('Gérant / directeur');
    $response->assertSee('invite l\'équipe', false);

    // M0b Créer établissement
    $response->assertSee('Créer votre clinique');
    $response->assertSee('Établissement');
    $response->assertSee('Responsable');
    $response->assertSee('Déjà inscrit ?');
    $response->assertSee('id="preview_caissier"', false);
    $response->assertSee('Aperçu en direct');
    $response->assertSee('id="preview_clinic_name"', false);

    // M0c Rejoindre avec code : uniquement des rôles qui existent dans l'application
    $response->assertSee('Code de la clinique');
    $response->assertSee('Rejoignez votre équipe en un instant.');
    $response->assertSee('Médecin');
    $response->assertSee('Réceptionniste');
    $response->assertSee('Caissier');
    $response->assertSee('Comptable');

    // M0d Vérification
    $response->assertSee('Vérifions votre identité');
    $response->assertSee('Accès vérifié');
});

test('le shell applicatif fournit une navigation mobile', function () {
    $clinique = creerClinique('Clinique Responsive', 'CRE');
    $role = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);
    $user = creerUtilisateur($clinique, 'resp@test.ml', $role);

    $reponse = $this->actingAs($user)->get(route('dashboard'));

    $reponse->assertOk()
        // Le bouton d ouverture du tiroir et son voile doivent etre presents
        ->assertSee('id="sidebarToggle"', false)
        ->assertSee('id="sidebarBackdrop"', false)
        // Le shell doit declarer des points de rupture, pas seulement l impression
        ->assertSee('@media (max-width: 991.98px)', false)
        ->assertSee('width=device-width', false);
});
