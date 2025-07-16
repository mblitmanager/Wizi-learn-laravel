<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run()
    {
        $achievements = [
            [
                'name' => 'Série de connexions',
                'type' => 'connexion_serie',
                'condition' => 5,
                'description' => 'Connectez-vous plusieurs jours d\'affilée',
                'icon' => '🔥',
            ],
            [
                'name' => 'Première connexion',
                'type' => 'connexion_serie',
                'condition' => 1,
                'description' => 'Connectez-vous pour la première fois',
                'icon' => '🎉',
            ],
            [
                'name' => 'Premier quiz',
                'type' => 'quiz',
                'condition' => 1,
                'description' => 'Terminez votre premier quiz',
                'icon' => '🏆',
            ],
            [
                'name' => 'Première vidéo',
                'type' => 'video',
                'condition' => 1,
                'description' => 'Regardez votre première vidéo',
                'icon' => '🎬',
            ],
            [
                'name' => 'Premier parrainage',
                'type' => 'parrainage',
                'condition' => 1,
                'description' => 'Parrainez un utilisateur pour la première fois',
                'icon' => '🤝',
            ],
        ];

        foreach ($achievements as $data) {
            Achievement::firstOrCreate([
                'name' => $data['name'],
                'type' => $data['type'],
            ], $data);
        }
    }
}
