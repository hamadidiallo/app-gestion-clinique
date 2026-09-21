<?php

use App\Models\Role;
use App\Models\User;
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
    $response->assertSee('Accédez à votre espace de travail.', false);
    $response->assertSee('Numéro de téléphone');
    $response->assertSee('Mot de passe');
    $response->assertSee('Oublié ?');
    $response->assertSee('Rester connecté sur ce poste');
    $response->assertSee('Se connecter');
    $response->assertSee('Connexion sécurisée · données de santé chiffrées');

    // Panneau droit : Showcase du périmètre fonctionnel
    $response->assertSee('Le dossier patient, du premier rendez-vous à la sortie.');
    $response->assertSee('Rendez-vous, consultations, ordonnances, pharmacie et facturation réunis sur un poste unique et sécurisé.');
    $response->assertSee('Constantes et antécédents centralisés');
    $response->assertSee('Ordonnances et comptes rendus imprimables');
    $response->assertSee('Tickets, encaissements et tiers payant');
    $response->assertSee('Traçabilité complète des accès');
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
    $response->assertSee('Votre établissement');
    $response->assertSee('Configuré en trois étapes.');
    $response->assertSee('Dossiers patients illimités');
    $response->assertSee('Ordonnances & facturation');
    $response->assertSee('Multi-utilisateurs par rôle');

    // M0c Rejoindre avec code
    $response->assertSee('Code de la clinique');
    $response->assertSee('Rejoignez votre équipe en un instant.');
    $response->assertSee('Médecin');
    $response->assertSee('Infirmier');
    $response->assertSee('Caissier');
    $response->assertSee('Laborantin');

    // M0d Vérification
    $response->assertSee('Vérifions votre identité');
    $response->assertSee('Données chiffrées');
});
