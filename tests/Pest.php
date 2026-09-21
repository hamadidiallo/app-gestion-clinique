<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Cas de test
|--------------------------------------------------------------------------
|
| Tous les tests du dossier Feature s'appuient sur TestCase et sur une base
| réinitialisée à chaque test.
|
| ATTENTION : un administrateur est connecté avant chaque test, et son
| clinique_id est nul. Le scope multi-clinique ne filtre alors rien, ce qui
| masque les défauts de cloisonnement. Les tests qui vérifient l'isolation ou
| l'authentification doivent donc appeler actingAs() ou auth()->logout()
| explicitement pour repartir d'un état maîtrisé.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        if (! isset($this->user)) {
            $role = Role::firstOrCreate(['nom' => 'Administrateur']);
            $user = User::firstOrCreate(
                ['email' => 'admin-test@example.com'],
                [
                    'nom' => 'Admin',
                    'prenom' => 'Test',
                    'password' => bcrypt('password'),
                    'role_id' => $role->id,
                ]
            );
            $this->actingAs($user);
        }
    })
    ->in('Feature');
