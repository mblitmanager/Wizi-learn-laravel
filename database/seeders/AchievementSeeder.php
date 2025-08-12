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
                'name' => 'Toutes les vidéos',
                'type' => 'video',
                'condition' => 0,
                'description' => 'Regardez toutes les vidéos du catalogue',
                'icon' => 'tv',
                'code' => 'all_videos',
            ],
            [
                'name' => 'Série de connexions',
                'type' => 'connexion_serie',
                'condition' => 5,
                'description' => 'Connectez-vous plusieurs jours d\'affilée',
                'icon' => 'fire',
                'code' => 'login_streak_5',
            ],
            [
                'name' => 'Première connexion',
                'type' => 'connexion_serie',
                'condition' => 1,
                'description' => 'Connectez-vous pour la première fois',
                'icon' => 'party',
                'code' => 'first_login',
            ],
            [
                'name' => 'Premier quiz',
                'type' => 'quiz',
                'condition' => 1,
                'description' => 'Terminez votre premier quiz',
                'icon' => 'trophy',
                'code' => 'first_quiz',
            ],
            [
                'name' => 'Première vidéo',
                'type' => 'video',
                'condition' => 1,
                'description' => 'Regardez votre première vidéo',
                'icon' => 'clapper',
                'code' => 'first_video',
            ],
            [
                'name' => 'Premier parrainage',
                'type' => 'parrainage',
                'condition' => 1,
                'description' => 'Parrainez un utilisateur pour la première fois',
                'icon' => 'handshake',
                'code' => 'first_referral',
            ],
            // Téléchargement app Android
            [
                'name' => 'Téléchargement de l\'application Android',
                'type' => 'action',
                'condition' => 1,
                'description' => 'A téléchargé l\'application Android depuis l\'accueil',
                'icon' => 'trophy',
                'code' => 'android_download',
            ],
            // Premier quiz par niveau
            ['name' => 'Premier quiz (Débutant)', 'type' => 'quiz_level', 'condition' => 1, 'description' => 'Terminez votre premier quiz débutant', 'icon' => 'bronze', 'code' => 'first_quiz_beginner', 'level' => 'débutant'],
            ['name' => 'Premier quiz (Intermédiaire)', 'type' => 'quiz_level', 'condition' => 1, 'description' => 'Terminez votre premier quiz intermédiaire', 'icon' => 'silver', 'code' => 'first_quiz_intermediate', 'level' => 'intermédiaire'],
            ['name' => 'Premier quiz (Avancé)', 'type' => 'quiz_level', 'condition' => 1, 'description' => 'Terminez votre premier quiz avancé', 'icon' => 'gold', 'code' => 'first_quiz_advanced', 'level' => 'avancé'],
            // Tous les quiz
            ['name' => 'Tous les quiz', 'type' => 'quiz_all', 'condition' => 0, 'description' => 'Terminez tous les quiz', 'icon' => 'trophy', 'code' => 'all_quizzes'],
            ['name' => 'Tous les quiz (Débutant)', 'type' => 'quiz_all_level', 'condition' => 0, 'description' => 'Terminez tous les quiz débutant', 'icon' => 'bronze', 'code' => 'all_quizzes_beginner', 'level' => 'débutant'],
            ['name' => 'Tous les quiz (Intermédiaire)', 'type' => 'quiz_all_level', 'condition' => 0, 'description' => 'Terminez tous les quiz intermédiaire', 'icon' => 'silver', 'code' => 'all_quizzes_intermediate', 'level' => 'intermédiaire'],
            ['name' => 'Tous les quiz (Avancé)', 'type' => 'quiz_all_level', 'condition' => 0, 'description' => 'Terminez tous les quiz avancé', 'icon' => 'gold', 'code' => 'all_quizzes_advanced', 'level' => 'avancé'],
            // Parrainages
            ['name' => '5ème parrainage', 'type' => 'parrainage', 'condition' => 5, 'description' => 'Réussissez 5 parrainages', 'icon' => 'handshake', 'code' => 'fifth_referral'],
            // Connexions séries
            ['name' => 'Connexion 5 jours', 'type' => 'connexion_serie', 'condition' => 5, 'description' => 'Connectez-vous 5 jours d\'affilée', 'icon' => 'fire', 'code' => 'login_streak_5'],
            ['name' => 'Connexion 7 jours', 'type' => 'connexion_serie', 'condition' => 7, 'description' => 'Connectez-vous 7 jours d\'affilée', 'icon' => 'fire', 'code' => 'login_streak_7'],
            ['name' => 'Connexion 30 jours', 'type' => 'connexion_serie', 'condition' => 30, 'description' => 'Connectez-vous 30 jours d\'affilée', 'icon' => 'fire', 'code' => 'login_streak_30'],
            ['name' => 'Connexion 3 mois', 'type' => 'connexion_serie', 'condition' => 90, 'description' => 'Connectez-vous 3 mois d\'affilée', 'icon' => 'fire', 'code' => 'login_streak_90'],
            ['name' => 'Connexion 6 mois', 'type' => 'connexion_serie', 'condition' => 180, 'description' => 'Connectez-vous 6 mois d\'affilée', 'icon' => 'fire', 'code' => 'login_streak_180'],
            ['name' => 'Connexion 1 an', 'type' => 'connexion_serie', 'condition' => 365, 'description' => 'Connectez-vous 1 an d\'affilée', 'icon' => 'fire', 'code' => 'login_streak_365'],
            // Points cumulés
            ['name' => '50 points gagnés', 'type' => 'points', 'condition' => 50, 'description' => 'Cumulez 50 points', 'icon' => 'bronze', 'code' => 'points_50'],
            ['name' => '100 points gagnés', 'type' => 'points', 'condition' => 100, 'description' => 'Cumulez 100 points', 'icon' => 'silver', 'code' => 'points_100'],
            ['name' => '200 points gagnés', 'type' => 'points', 'condition' => 200, 'description' => 'Cumulez 200 points', 'icon' => 'gold', 'code' => 'points_200'],
        ];

        foreach ($achievements as $data) {
            if (!empty($data['code'])) {
                Achievement::updateOrCreate([
                    'code' => $data['code'],
                ], $data);
            } else {
                Achievement::updateOrCreate([
                    'name' => $data['name'],
                    'type' => $data['type'],
                ], $data);
            }
        }
    }
}
