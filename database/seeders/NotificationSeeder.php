<?php

namespace Database\Seeders;

use App\Models\CatalogueFormation;
use App\Models\Media;
use App\Models\Notification;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('email', 'like', 'stagiaire%@wizi-learn.com')->orderBy('id')->take(10)->get();
        if ($users->isEmpty()) {
            return;
        }

        $quiz = Quiz::orderBy('id')->first();
        $quizExcel = Quiz::where('titre', 'like', 'Excel%')->first() ?? $quiz;
        $formation = CatalogueFormation::orderBy('id')->first();
        $media = Media::orderBy('id')->first();

        // [type, message, data, lue]
        $notifications = [
            ['quiz', "Un nouveau quiz est disponible : « {$quiz?->titre} » !", ['quiz_id' => $quiz?->id, 'quiz_title' => $quiz?->titre], false],
            ['quiz', "Vous avez obtenu 8/10 au quiz « {$quizExcel?->titre} ».", ['quiz_id' => $quizExcel?->id, 'quiz_title' => $quizExcel?->titre, 'score' => 8, 'total_questions' => 5], true],
            ['formation', "Votre formation « {$formation?->titre} » commence la semaine prochaine.", ['formation_id' => $formation?->id, 'formation_title' => $formation?->titre], false],
            ['media', "Nouvelle vidéo disponible : « {$media?->titre} ».", ['media_id' => $media?->id, 'media_title' => $media?->titre], false],
            ['badge', 'Félicitations ! Vous avez débloqué le badge « Premier quiz ».', ['achievement_code' => 'first_quiz'], true],
            ['badge', 'Bravo, 5 jours de connexion d\'affilée !', ['achievement_code' => 'login_streak_5'], false],
            ['parrainage', 'Votre filleul s\'est inscrit : vous gagnez 2 points et 50 € !', ['points' => 2, 'gains' => 50], false],
            ['formation', 'Votre rendez-vous de suivi avec votre formateur est confirmé.', ['action' => 'agenda'], true],
            ['system', 'Bienvenue sur Wizi Learn ! Commencez votre parcours d\'apprentissage.', ['action' => 'welcome'], true],
            ['system', 'Pensez à compléter votre profil pour profiter de toutes les fonctionnalités.', ['action' => 'profile'], false],
        ];

        foreach ($notifications as $i => [$type, $message, $data, $read]) {
            Notification::updateOrCreate(
                ['user_id' => $users[$i % $users->count()]->id, 'message' => $message],
                ['type' => $type, 'data' => $data, 'read' => $read]
            );
        }
    }
}
