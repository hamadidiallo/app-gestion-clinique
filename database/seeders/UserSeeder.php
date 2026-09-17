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
        $adminRole = Role::where('nom', 'Administrateur')->first();

        if ($adminRole) {
            User::updateOrCreate(
                ['email' => 'admin@clinique.local'],
                [
                    'nom' => 'Admin',
                    'prenom' => 'Principal',
                    'email' => 'admin@clinique.local',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                ]
            );
        }
    }
}
