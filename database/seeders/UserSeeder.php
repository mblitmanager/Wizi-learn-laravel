<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Créer l'admin
        User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@wizi-learn.com',
            'password' => Hash::make('password'),
            'role' => 'administrateur',
            'image' => null,
        ]);

        // Créer 20 stagiaires
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => "Stagiaire $i",
                'email' => "stagiaire$i@wizi-learn.com",
                'password' => Hash::make('password'),
                'role' => 'stagiaire',
                'image' => null,
            ]);
        }
    }
}
