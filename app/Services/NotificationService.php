<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function notifyQuizAvailable(string $quizTitle, int $quizId): void
    {
        // Créer une notification pour tous les utilisateurs
        $users = User::where('role', 'stagiaire')->get();

        foreach ($users as $user) {
            // Récupérer la formation active du stagiaire via la relation Stagiaire
            $stagiaire = $user->stagiaire;
            $formation = null;
            if ($stagiaire) {
                $formation = $stagiaire->catalogue_formations();//->wherePivot('status', 'active')->first();
            }
            if ($formation) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'quiz',
                'message' => "Un nouveau quiz \"{$quizTitle}\" est disponible !",
                'data' => [
                'quiz_id' => $quizId,
                'quiz_title' => $quizTitle
                ],
                'read' => false
            ]);
            }
        }

    }

    public function notifyQuizCompleted(int $userId, int $quizId, int $score, int $totalQuestions): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => 'quiz',
            'message' => "Vous avez obtenu {$score}/{$totalQuestions} points au quiz !",
            'data' => [
                'quiz_id' => $quizId,
                'score' => $score,
                'total_questions' => $totalQuestions
            ],
            'read' => false
        ]);
    }

    public function notifyRewardEarned(int $userId, int $points, ?string $rewardType = null): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => 'badge',
            'message' => "Vous avez gagné {$points} points" . ($rewardType ? " et un {$rewardType}" : "") . " !",
            'data' => [
                'points' => $points,
                'reward_type' => $rewardType
            ],
            'read' => false
        ]);
    }

    public function notifyFormationUpdate(int $userId, string $formationTitle, int $formationId): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => 'formation',
            'message' => "La formation \"{$formationTitle}\" a été mise à jour !",
            'data' => [
                'formation_id' => $formationId,
                'formation_title' => $formationTitle
            ],
            'read' => false
        ]);
    }
}
