<?php

use App\Models\Clinique;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['nom' => 'Administrateur']);
    $this->caissierRole = Role::firstOrCreate(['nom' => 'Caissier']);

    $this->clinique = Clinique::create([
        'nom' => 'Clinique Espoir Bamako',
        'slug' => 'clinique-espoir-bamako',
        'code' => 'CEB',
        'ville' => 'Bamako',
        'pays' => 'Mali',
        'plan' => 'pro',
        'statut' => 'actif',
        'prefixe_ticket' => 'TCK',
        'prefixe_patient' => 'PAT',
        'code_invitation' => 'CEB-1234',
    ]);

    $this->adminUser = User::create([
        'clinique_id' => $this->clinique->id,
        'nom' => 'Diallo',
        'prenom' => 'Amadou',
        'email' => 'admin@clinique-espoir.ml',
        'password' => bcrypt('password'),
        'role_id' => $this->adminRole->id,
    ]);

    $this->caissierUser = User::create([
        'clinique_id' => $this->clinique->id,
        'nom' => 'Traoré',
        'prenom' => 'Fatoumata',
        'email' => 'caisse@clinique-espoir.ml',
        'password' => bcrypt('password'),
        'role_id' => $this->caissierRole->id,
    ]);
});

test('un invite est redirige vers login lorsqu il tente d acceder aux abonnements', function () {
    Auth::logout();

    $response = $this->get(route('abonnement.index'));
    $response->assertRedirect(route('login'));
});

test('un utilisateur non admin ne peut pas acceder a la page abonnement', function () {
    $response = $this->actingAs($this->caissierUser)->get(route('abonnement.index'));
    $response->assertStatus(403);
});

test('un administrateur accede a la page abonnement et voit les formules et tarifs', function () {
    $response = $this->actingAs($this->adminUser)->get(route('abonnement.index'));

    $response->assertStatus(200);
    $response->assertSee('Abonnement & Formules de Licence');
    $response->assertSee('Formule Cabinet / Starter');
    $response->assertSee('Formule Pro / Clinique Médicale');
    $response->assertSee('Formule Entreprise / Hôpital & Groupe');
    $response->assertSee('35 000');
    $response->assertSee('75 000');
    $response->assertSee('150 000');
    $response->assertSee('Clinique Espoir Bamako');
});
