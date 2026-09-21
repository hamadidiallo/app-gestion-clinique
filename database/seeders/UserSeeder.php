<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garde-fou : ces comptes ont un mot de passe connu, ils n'ont rien à faire en production.
        if (app()->environment('production')) {
            $this->command?->warn('UserSeeder ignoré : comptes de démonstration interdits en production.');

            return;
        }

        // Mot de passe surchargeable via SEED_PASSWORD pour éviter une valeur devinable partagée.
        $motDePasse = env('SEED_PASSWORD', 'demo-local-2026');

        $roles = Role::all()->keyBy('nom');

        $users = [
            [
                'nom' => 'Admin',
                'prenom' => 'Principal',
                'email' => 'admin@clinique.local',
                'telephone' => '76000001',
                'role' => 'Administrateur',
            ],
            [
                'nom' => 'Diallo',
                'prenom' => 'Dr Alpha',
                'email' => 'medecin@clinique.local',
                'telephone' => '76000002',
                'role' => 'Médecin',
            ],
            [
                'nom' => 'Traoré',
                'prenom' => 'Fatou',
                'email' => 'caissier@clinique.local',
                'telephone' => '76000003',
                'role' => 'Caissier',
            ],
            [
                'nom' => 'Coulibaly',
                'prenom' => 'Awa',
                'email' => 'reception@clinique.local',
                'telephone' => '76000004',
                'role' => 'Réceptionniste',
            ],
            [
                'nom' => 'Keita',
                'prenom' => 'Moussa',
                'email' => 'comptable@clinique.local',
                'telephone' => '76000005',
                'role' => 'Comptable',
            ],
        ];

        foreach ($users as $userData) {
            $roleModel = $roles->get($userData['role']);

            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'nom' => $userData['nom'],
                    'prenom' => $userData['prenom'],
                    'telephone' => $userData['telephone'],
                    'password' => Hash::make($motDePasse),
                    'role_id' => $roleModel?->id,
                ]
            );
        }
    }
}
