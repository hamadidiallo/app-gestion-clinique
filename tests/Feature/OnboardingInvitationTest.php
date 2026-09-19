<?php

use App\Models\Clinique;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(
        ['nom' => 'Administrateur'],
        ['description' => 'Administration de la clinique']
    );

    $this->medecinRole = Role::firstOrCreate(
        ['nom' => 'Médecin'],
        ['description' => 'Corps médical et consultations']
    );

    $this->clinique = Clinique::create([
        'nom' => 'Clinique Médico-Chirurgicale Pasteur',
        'slug' => 'clinique-pasteur',
        'code' => 'CPAS',
        'ville' => 'Bamako',
        'pays' => 'Mali',
        'devise' => 'FCFA',
        'statut' => 'actif',
        'prefixe_ticket' => 'TCK',
        'prefixe_patient' => 'PAT',
        'code_invitation' => 'CPAS-9988',
    ]);

    Auth::logout();
});

test('la page inscription affiche les options de rejoindre et de creer une clinique', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
    $response->assertSee('Rejoindre une clinique');
    $response->assertSee('Créer une clinique');
    $response->assertSee('code_invitation');
    $response->assertSee('nom_clinique');
});

test('un collaborateur peut rejoindre une clinique avec un code invitation valide', function () {
    $response = $this->post(route('register'), [
        'action_type' => 'rejoindre',
        'code_invitation' => 'CPAS-9988',
        'prenom' => 'Aïssata',
        'nom' => 'Diarra',
        'email' => 'aissata.diarra@pasteur.ml',
        'role_id' => $this->medecinRole->id,
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $user = User::where('email', 'aissata.diarra@pasteur.ml')->first();
    expect($user)->not->toBeNull()
        ->and($user->clinique_id)->toBe($this->clinique->id)
        ->and($user->role_id)->toBe($this->medecinRole->id)
        ->and($user->nom)->toBe('Diarra');
});

test('un code invitation erroné est rejeté avec un message clair', function () {
    $response = $this->from(route('register'))->post(route('register'), [
        'action_type' => 'rejoindre',
        'code_invitation' => 'CODE-INEXISTANT-0000',
        'prenom' => 'Jean',
        'nom' => 'Dupont',
        'email' => 'jean@example.com',
        'role_id' => $this->medecinRole->id,
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertRedirect(route('register'));
    $response->assertSessionHasErrors('code_invitation');
    $this->assertGuest();
});

test('une clinique suspendue ne permet pas le rattachement de nouveaux utilisateurs', function () {
    $this->clinique->update(['statut' => 'suspendu']);

    $response = $this->from(route('register'))->post(route('register'), [
        'action_type' => 'rejoindre',
        'code_invitation' => 'CPAS-9988',
        'prenom' => 'Oumar',
        'nom' => 'Sissoko',
        'email' => 'oumar@pasteur.ml',
        'role_id' => $this->medecinRole->id,
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertRedirect(route('register'));
    $response->assertSessionHasErrors('code_invitation');
    $this->assertGuest();
});

test('un directeur peut creer sa clinique et devient administrateur avec un code invitation unique généré', function () {
    $response = $this->post(route('register'), [
        'action_type' => 'creer',
        'nom_clinique' => 'Polyclinique Sainte Marie',
        'ville' => 'Ségou',
        'pays' => 'Mali',
        'telephone_clinique' => '+223 21 32 01 02',
        'prenom' => 'Dr Ibrahima',
        'nom' => 'Kone',
        'email' => 'direction@stemarie.ml',
        'password' => 'directeur2026',
        'password_confirmation' => 'directeur2026',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $user = User::where('email', 'direction@stemarie.ml')->first();
    expect($user)->not->toBeNull()
        ->and($user->role_id)->toBe($this->adminRole->id);

    $nouvelleClinique = Clinique::where('nom', 'Polyclinique Sainte Marie')->first();
    expect($nouvelleClinique)->not->toBeNull()
        ->and($user->clinique_id)->toBe($nouvelleClinique->id)
        ->and($nouvelleClinique->code_invitation)->not->toBeEmpty()
        ->and($nouvelleClinique->statut)->toBe('actif');
});

test('l administrateur voit le code invitation de sa clinique sur la page de gestion des utilisateurs', function () {
    $admin = User::create([
        'clinique_id' => $this->clinique->id,
        'nom' => 'Directeur',
        'prenom' => 'Principal',
        'email' => 'admin@pasteur.ml',
        'password' => bcrypt('password'),
        'role_id' => $this->adminRole->id,
    ]);

    $response = $this->actingAs($admin)->get(route('users.index'));

    $response->assertStatus(200);
    $response->assertSee('CPAS-9988');
    $response->assertSee("Code d'invitation Clinique", false);
    $response->assertSee('Copier');
});

test('l endpoint de verification de code retourne les informations de la clinique en JSON', function () {
    $response = $this->getJson(route('auth.verifier-code', ['code' => 'CPAS-9988']));

    $response->assertStatus(200)
        ->assertJson([
            'valide' => true,
            'nom' => 'Clinique Médico-Chirurgicale Pasteur',
            'ville' => 'Bamako',
        ]);

    $responseInvalide = $this->getJson(route('auth.verifier-code', ['code' => 'FAUX-CODE']));
    $responseInvalide->assertStatus(200)
        ->assertJson([
            'valide' => false,
        ]);
});
