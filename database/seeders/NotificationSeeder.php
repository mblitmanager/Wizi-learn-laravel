<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer tous les utilisateurs stagiaires
        $users = User::where('role', 'stagiaire')->get();

        foreach ($users as $user) {
            // Notifications de quiz
            Notification::create([
                'user_id' => $user->id,
                'type' => 'quiz',
                'message' => 'Un nouveau quiz sur JavaScript est disponible !',
                'data' => [
                    'quiz_id' => 1,
                    'quiz_title' => 'Introduction à JavaScript'
                ],
                'read' => false
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'quiz',
                'message' => 'Vous avez obtenu 8/10 points au quiz PHP !',
                'data' => [
                    'quiz_id' => 2,
                    'quiz_title' => 'Les bases de PHP',
                    'score' => 8,
                    'total_questions' => 10
                ],
                'read' => true
            ]);

            // Notifications de formation
            Notification::create([
                'user_id' => $user->id,
                'type' => 'formation',
                'message' => 'La formation "Développement Web" a été mise à jour !',
                'data' => [
                    'formation_id' => 1,
                    'formation_title' => 'Développement Web'
                ],
                'read' => false
            ]);

            // Notifications de récompenses
            Notification::create([
                'user_id' => $user->id,
                'type' => 'badge',
                'message' => 'Félicitations ! Vous avez gagné le badge "Expert JavaScript" !',
                'data' => [
                    'badge_id' => 1,
                    'badge_name' => 'Expert JavaScript',
                    'points' => 100
                ],
                'read' => false
            ]);

            // Notifications système
            Notification::create([
                'user_id' => $user->id,
                'type' => 'system',
                'message' => 'Bienvenue sur Wizi Learn ! Commencez votre parcours d\'apprentissage.',
                'data' => [
                    'action' => 'welcome'
                ],
                'read' => true
            ]);
        }
    }
}
