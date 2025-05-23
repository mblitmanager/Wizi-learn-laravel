<?php

namespace Database\Seeders;

use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StagiaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Récupérer tous les users stagiaires
        $stagiaireUsers = User::where('role', 'stagiaire')->get();

        foreach ($stagiaireUsers as $user) {
            Stagiaire::create([
                'civilite' => 'M',
                'prenom' => $user->name,
                'telephone' => '060000000' . rand(0, 9),
                'adresse' => '1 rue de la Formation',
                'date_naissance' => now()->subYears(rand(18, 35))->subDays(rand(0, 365)),
                'ville' => 'Paris',
                'code_postal' => '75000',
                'date_debut_formation' => now()->subDays(rand(0, 30)),
                'date_inscription' => now()->subDays(rand(0, 60)),
                'role' => 'stagiaire',
                'statut' => true,
                'user_id' => $user->id,
            ]);
        }
    }
}
