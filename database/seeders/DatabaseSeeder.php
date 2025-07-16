<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StagiaireSeeder::class,
            FormationSeeder::class,
            NotificationSeeder::class,
            TeamSeeder::class,
        ]);

        // Génération automatique des achievements streak (connexion_serie)
        \App\Models\Achievement::updateOrCreate([
            'type' => 'connexion_serie',
            'condition' => 5
        ], [
            'name' => 'Série de 5 jours',
            'description' => 'Se connecter 5 jours d\'affilée',
            'icon' => '🔥',
            'level' => 'bronze',
        ]);
        \App\Models\Achievement::updateOrCreate([
            'type' => 'connexion_serie',
            'condition' => 10
        ], [
            'name' => 'Série de 10 jours',
            'description' => 'Se connecter 10 jours d\'affilée',
            'icon' => '🔥',
            'level' => 'silver',
        ]);
        \App\Models\Achievement::updateOrCreate([
            'type' => 'connexion_serie',
            'condition' => 30
        ], [
            'name' => 'Série de 30 jours',
            'description' => 'Se connecter 30 jours d\'affilée',
            'icon' => '🔥',
            'level' => 'gold',
        ]);
    }
}
