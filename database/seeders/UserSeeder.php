<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Compte administrateur. Les autres utilisateurs sont créés par les seeders
 * de chaque profil (Formateur, Commercial, PoleRelationClient, Stagiaire).
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@wizi-learn.com'],
            [
                'name' => 'Admin Principal',
                'password' => Hash::make('password'),
                'role' => 'administrateur',
                'email_verified_at' => now(),
            ]
        );
    }
}
