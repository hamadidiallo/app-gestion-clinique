<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $medecin;

    protected User $caissier;

    protected User $receptionniste;

    protected User $comptable;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['nom' => 'Administrateur']);
        $roleMedecin = Role::create(['nom' => 'Médecin']);
        $roleCaissier = Role::create(['nom' => 'Caissier']);
        $roleReceptionniste = Role::create(['nom' => 'Réceptionniste']);
        $roleComptable = Role::create(['nom' => 'Comptable']);

        $this->admin = User::create([
            'nom' => 'Admin',
            'prenom' => 'Super',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleAdmin->id,
        ]);

        $this->medecin = User::create([
            'nom' => 'Diallo',
            'prenom' => 'Dr Alpha',
            'email' => 'medecin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleMedecin->id,
        ]);

        $this->caissier = User::create([
            'nom' => 'Traoré',
            'prenom' => 'Fatou',
            'email' => 'caissier@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleCaissier->id,
        ]);

        $this->receptionniste = User::create([
            'nom' => 'Coulibaly',
            'prenom' => 'Awa',
            'email' => 'reception@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleReceptionniste->id,
        ]);

        $this->comptable = User::create([
            'nom' => 'Keita',
            'prenom' => 'Moussa',
            'email' => 'comptable@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleComptable->id,
        ]);
    }

    /** Un invité non connecté est redirigé vers le login */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));

        $responseAdmin = $this->get(route('users.index'));
        $responseAdmin->assertRedirect(route('login'));
    }

    /** L'administrateur a accès à l'ensemble du système */
    public function test_admin_has_full_access_to_all_modules(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('dashboard'))->assertStatus(200);
        $this->get(route('users.index'))->assertStatus(200);
        $this->get(route('roles.index'))->assertStatus(200);
        $this->get(route('caisses.index'))->assertStatus(200);
        $this->get(route('recettes.index'))->assertStatus(200);
        $this->get(route('consultations.index'))->assertStatus(200);
    }

    /** Le Caissier a accès à la caisse et aux paiements, mais PAS à l'administration ni à la compta */
    public function test_caissier_access_permissions(): void
    {
        $this->actingAs($this->caissier);

        // Autorisé
        $this->get(route('caisses.index'))->assertStatus(200);
        $this->get(route('paiements.index'))->assertStatus(200);
        $this->get(route('dettes.index'))->assertStatus(200);

        // Interdit (403 Forbidden)
        $this->get(route('users.index'))->assertStatus(403);
        $this->get(route('roles.index'))->assertStatus(403);
        $this->get(route('recettes.index'))->assertStatus(403);
        $this->get(route('depenses.index'))->assertStatus(403);
        $this->get(route('consultations.index'))->assertStatus(403);
    }

    /** Le Médecin a accès aux consultations et patients, mais PAS à la caisse ni aux utilisateurs */
    public function test_medecin_access_permissions(): void
    {
        $this->actingAs($this->medecin);

        // Autorisé
        $this->get(route('consultations.index'))->assertStatus(200);
        $this->get(route('patients.index'))->assertStatus(200);
        $this->get(route('remunerations.index'))->assertStatus(200);

        // Interdit (403 Forbidden)
        $this->get(route('users.index'))->assertStatus(403);
        $this->get(route('caisses.index'))->assertStatus(403);
        $this->get(route('recettes.index'))->assertStatus(403);
    }

    /** Le Réceptionniste a accès aux patients et tickets, mais PAS à la caisse ni aux dépenses */
    public function test_receptionniste_access_permissions(): void
    {
        $this->actingAs($this->receptionniste);

        // Autorisé
        $this->get(route('patients.index'))->assertStatus(200);
        $this->get(route('tickets.index'))->assertStatus(200);
        $this->get(route('tickets.create'))->assertStatus(200);

        // Interdit (403 Forbidden)
        $this->get(route('caisses.index'))->assertStatus(403);
        $this->get(route('recettes.index'))->assertStatus(403);
        $this->get(route('depenses.index'))->assertStatus(403);
        $this->get(route('users.index'))->assertStatus(403);
    }

    /** Le Comptable a accès aux finances et caisses, mais PAS à l'administration des utilisateurs */
    public function test_comptable_access_permissions(): void
    {
        $this->actingAs($this->comptable);

        // Autorisé
        $this->get(route('recettes.index'))->assertStatus(200);
        $this->get(route('depenses.index'))->assertStatus(200);
        $this->get(route('caisses.index'))->assertStatus(200);
        $this->get(route('remunerations.index'))->assertStatus(200);

        // Interdit (403 Forbidden)
        $this->get(route('users.index'))->assertStatus(403);
        $this->get(route('roles.index'))->assertStatus(403);
        $this->get(route('consultations.index'))->assertStatus(403);
    }
}
