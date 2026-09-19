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
        $roles = Role::all()->keyBy('nom');

        $users = [
            [
                'nom' => 'Admin',
                'prenom' => 'Principal',
                'email' => 'admin@clinique.local',
                'role' => 'Administrateur',
            ],
            [
                'nom' => 'Diallo',
                'prenom' => 'Dr Alpha',
                'email' => 'medecin@clinique.local',
                'role' => 'Médecin',
            ],
            [
                'nom' => 'Traoré',
                'prenom' => 'Fatou',
                'email' => 'caissier@clinique.local',
                'role' => 'Caissier',
            ],
            [
                'nom' => 'Coulibaly',
                'prenom' => 'Awa',
                'email' => 'reception@clinique.local',
                'role' => 'Réceptionniste',
            ],
            [
                'nom' => 'Keita',
                'prenom' => 'Moussa',
                'email' => 'comptable@clinique.local',
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
                    'password' => Hash::make('password'),
                    'role_id' => $roleModel?->id,
                ]
            );
        }
    }
}
