<?php

use App\Models\Clinique;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Cas de test
|--------------------------------------------------------------------------
|
| Les tests Feature s'appuient sur TestCase et sur une base réinitialisée à
| chaque test. Aucun utilisateur n'est connecté automatiquement : chaque test
| déclare qui il est, faute de quoi le cloisonnement multi-clinique ne serait
| pas réellement exercé et les régressions d'isolation passeraient inaperçues.
|
| Utiliser connecterAdmin() pour obtenir une clinique et son administrateur.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Fabriques de test
|--------------------------------------------------------------------------
*/

/**
 * Crée une clinique de test.
 */
function creerClinique(string $nom = 'Clinique de Test', string $code = 'CLT'): Clinique
{
    return Clinique::create([
        'nom' => $nom,
        'slug' => Str::slug($nom),
        'code' => $code,
        'ville' => 'Bamako',
        'pays' => 'Mali',
        'devise' => 'FCFA',
        'statut' => 'actif',
        'prefixe_ticket' => 'TCK',
        'prefixe_patient' => 'PAT',
        'code_invitation' => $code.'-AAAAAA',
    ]);
}

/**
 * Crée un utilisateur rattaché à une clinique.
 */
function creerUtilisateur(Clinique $clinique, string $email, Role $role): User
{
    return User::create([
        'clinique_id' => $clinique->id,
        'nom' => 'Nom',
        'prenom' => 'Prenom',
        'email' => $email,
        'password' => bcrypt('password'),
        'role_id' => $role->id,
    ]);
}

/**
 * Crée une clinique et son administrateur, puis ouvre une session pour lui.
 *
 * Le compte est rattaché à une clinique réelle : le scope multi-clinique
 * s'applique donc pendant le test, contrairement à un compte sans clinique.
 */
function connecterAdmin(?Clinique $clinique = null): User
{
    $clinique ??= creerClinique();

    $role = Role::firstOrCreate(
        ['nom' => 'Administrateur'],
        ['description' => 'Administration complète de la clinique']
    );

    $admin = creerUtilisateur($clinique, 'admin@'.$clinique->slug.'.test', $role);

    test()->actingAs($admin);

    return $admin;
}
