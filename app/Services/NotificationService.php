<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\TestNotification;
use Carbon\Carbon;

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
                // Récupérer la formation active du stagiaire
                $formation = $stagiaire->catalogue_formations()->get();//->wherePivot('status', 'active')->first();
            }
            dd($stagiaire->catalogue_formations());
            if ($formation && isset($formation->id)) {
                
                // Supposons que le quiz a une relation 'formation_id'
                $quizFormationId = \App\Models\Quiz::find($quizId)?->formation_id;
                if ($quizFormationId && $quizFormationId == $formation->id) {
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

                 
                    // Broadcast real-time notification via Pusher
                    event(new TestNotification([
                        'type' => 'quiz',
                        'message' => "Un nouveau quiz \"{$quizTitle}\" est disponible !",
                        // 'quiz_id' => $quizId,
                        'quiz_title' => $quizTitle,
                        // 'user_id' => $user->id
                    ]));
                }
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
                // 'quiz_id' => $quizId,
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

    public function notifyFormationUpdate(int $userId, string $formationTitle, int $formationId, ?string $dateDebut = null): void
    {
        $message = $dateDebut
            ? "Votre formation \"{$formationTitle}\" commence le {$dateDebut} !"
            : "La formation \"{$formationTitle}\" a été mise à jour !";
        Notification::create([
            'user_id' => $userId,
            'type' => 'formation',
            'message' => $message,
            'data' => [
                'formation_id' => $formationId,
                'formation_title' => $formationTitle,
                'date_debut' => $dateDebut
            ],
            'read' => false
        ]);
        // Optionnel : broadcast Pusher
        event(new \App\Events\TestNotification([
            'type' => 'formation',
            'message' => $message,
            // 'formation_id' => $formationId,
            'formation_title' => $formationTitle,
            'date_debut' => $dateDebut,
            // 'user_id' => $userId
        ]));
    }

    public function notifyMediaCreated(int $userId, string $mediaTitle, int $mediaId): void
    {
        $message = "Un nouveau média \"{$mediaTitle}\" a été ajouté !";
        Notification::create([
            'user_id' => $userId,
            'type' => 'media',
            'message' => $message,
            'data' => [
                'media_id' => $mediaId,
                'media_title' => $mediaTitle
            ],
            'read' => false
        ]);
        // Broadcast temps réel Pusher
        event(new \App\Events\TestNotification([
            'type' => 'media',
            'message' => $message,
            // 'media_id' => $mediaId,
            'media_title' => $mediaTitle,
            // 'user_id' => $userId
        ]));
    }
}
