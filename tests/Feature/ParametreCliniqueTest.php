<?php

use App\Models\Clinique;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['nom' => 'Administrateur']);
    $this->caissierRole = Role::firstOrCreate(['nom' => 'Caissier']);

    $this->clinique = Clinique::create([
        'nom' => 'Clinique Médico-Chirurgicale Pasteur',
        'slug' => 'clinique-pasteur',
        'code' => 'CPAS',
        'type_etablissement' => 'Policlinique',
        'ville' => 'Bamako',
        'pays' => 'Mali',
        'telephone' => '+223 20 22 11 00',
        'email' => 'direction@pasteur.ml',
        'devise' => 'FCFA',
        'statut' => 'actif',
        'prefixe_ticket' => 'TCK',
        'prefixe_patient' => 'PAT',
        'code_invitation' => 'CPAS-9988',
    ]);

    $this->adminUser = User::create([
        'clinique_id' => $this->clinique->id,
        'nom' => 'Diallo',
        'prenom' => 'Amadou',
        'email' => 'admin@pasteur.ml',
        'password' => bcrypt('password'),
        'role_id' => $this->adminRole->id,
    ]);

    $this->caissierUser = User::create([
        'clinique_id' => $this->clinique->id,
        'nom' => 'Traoré',
        'prenom' => 'Bakary',
        'email' => 'caissier@pasteur.ml',
        'password' => bcrypt('password'),
        'role_id' => $this->caissierRole->id,
    ]);
});

test('un invite est redirige vers login lorsqu il tente d acceder aux parametres', function () {
    Auth::logout();

    $response = $this->get(route('parametres.index'));
    $response->assertRedirect(route('login'));
});

test('un utilisateur non admin ne peut pas acceder aux parametres', function () {
    $response = $this->actingAs($this->caissierUser)->get(route('parametres.index'));
    $response->assertStatus(403);
});

test('l administrateur accede a l ecran de parametres de sa clinique', function () {
    $response = $this->actingAs($this->adminUser)->get(route('parametres.index'));

    $response->assertStatus(200);
    $response->assertSee('Paramètres de la Clinique');
    $response->assertSee('Clinique Médico-Chirurgicale Pasteur');
    $response->assertSee('CPAS-9988');
    $response->assertSee('Bamako');
    $response->assertSee('FCFA');
});

test('l administrateur peut modifier les informations et la devise de la clinique', function () {
    $response = $this->actingAs($this->adminUser)->put(route('parametres.update'), [
        'nom' => 'Nouvelle Polyclinique Pasteur Bamako',
        'type_etablissement' => 'Policlinique',
        'telephone' => '+223 20 22 99 99',
        'email' => 'contact@nouveau-pasteur.ml',
        'adresse' => 'Hamdallaye ACI 2000',
        'ville' => 'Bamako',
        'pays' => 'Mali',
        'devise' => 'FCFA',
        'prefixe_ticket' => 'TCK',
        'prefixe_patient' => 'PAT',
    ]);

    $response->assertRedirect(route('parametres.index'));
    $response->assertSessionHas('alert');

    $this->clinique->refresh();
    expect($this->clinique->nom)->toBe('Nouvelle Polyclinique Pasteur Bamako')
        ->and($this->clinique->telephone)->toBe('+223 20 22 99 99')
        ->and($this->clinique->adresse)->toBe('Hamdallaye ACI 2000');
});

test('l administrateur peut uploader un logo officiel pour sa clinique', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('mon-logo-clinique.png', 100, 'image/png');

    $response = $this->actingAs($this->adminUser)->put(route('parametres.update'), [
        'nom' => $this->clinique->nom,
        'ville' => $this->clinique->ville,
        'pays' => $this->clinique->pays,
        'devise' => 'FCFA',
        'prefixe_ticket' => 'TCK',
        'prefixe_patient' => 'PAT',
        'logo' => $file,
    ]);

    $response->assertRedirect(route('parametres.index'));
    $this->clinique->refresh();

    expect($this->clinique->logo)->not->toBeNull();
    Storage::disk('public')->assertExists($this->clinique->logo);
});

test('l administrateur peut supprimer le logo actuel', function () {
    Storage::fake('public');

    $this->clinique->update(['logo' => 'logos/test_logo.png']);
    Storage::disk('public')->put('logos/test_logo.png', 'fake image content');

    $response = $this->actingAs($this->adminUser)->put(route('parametres.update'), [
        'nom' => $this->clinique->nom,
        'ville' => $this->clinique->ville,
        'pays' => $this->clinique->pays,
        'devise' => 'FCFA',
        'prefixe_ticket' => 'TCK',
        'prefixe_patient' => 'PAT',
        'supprimer_logo' => '1',
    ]);

    $response->assertRedirect(route('parametres.index'));
    $this->clinique->refresh();

    expect($this->clinique->logo)->toBeNull();
    Storage::disk('public')->assertMissing('logos/test_logo.png');
});

test('l administrateur peut regenerer le code invitation de sa clinique', function () {
    $ancienCode = $this->clinique->code_invitation;

    $response = $this->actingAs($this->adminUser)->post(route('parametres.regenerer-code'));

    $response->assertRedirect(route('parametres.index'));
    $response->assertSessionHas('alert');

    $this->clinique->refresh();
    expect($this->clinique->code_invitation)->not->toBe($ancienCode)
        ->and($this->clinique->code_invitation)->not->toBeEmpty();
});
