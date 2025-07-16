<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Stagiaire;
// use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    /**
     * Vérifie et met à jour les succès du stagiaire, y compris ceux liés à la réussite d'un quiz
     * @param Stagiaire $stagiaire
     * @param int|null $quizId (optionnel) : ID du quiz qui vient d'être complété
     * @return array Liste des succès nouvellement débloqués
     */
    public function checkAchievements(Stagiaire $stagiaire, $quizId = null)
    {
        $newAchievements = [];
        $now = Carbon::now();

        // 1. Série de connexions journalières
        $lastLogin = $stagiaire->last_login_at ? Carbon::parse($stagiaire->last_login_at) : null;
        $streak = $stagiaire->login_streak ?? 0;
        if ($lastLogin) {
            if ($lastLogin->isToday()) {
                // rien à faire
            } elseif ($lastLogin->isYesterday()) {
                $streak++;
            } else {
                $streak = 1;
            }
        } else {
            $streak = 1;
        }
        // Mettre à jour le streak
        $stagiaire->login_streak = $streak;
        $stagiaire->last_login_at = $now;
        $stagiaire->save();

        // 2. Points totaux
        $totalPoints = $stagiaire->classements()->sum('points');

        // 3. Palier atteint
        $level = 'bronze';
        if ($totalPoints >= 200) {
            $level = 'gold';
        } elseif ($totalPoints >= 150) {
            $level = 'silver';
        }

        // Récupérer tous les succès
        $achievements = Achievement::all();
        $alreadyUnlocked = $stagiaire->achievements->pluck('id')->toArray();

        foreach ($achievements as $achievement) {
            $unlocked = false;
            switch ($achievement->type) {
                case 'connexion_serie':
                    if ($streak >= $achievement->condition) {
                        $unlocked = true;
                    }
                    break;
                case 'points_total':
                    if ($totalPoints >= $achievement->condition) {
                        $unlocked = true;
                    }
                    break;
                case 'palier':
                    if ($level === $achievement->level) {
                        $unlocked = true;
                    }
                    break;
                case 'quiz':
                    // Succès lié à un quiz spécifique
                    if ($quizId && $achievement->quiz_id == $quizId) {
                        $unlocked = true;
                    }
                    break;
            }
            if ($unlocked && !in_array($achievement->id, $alreadyUnlocked)) {
                // Ajoute le succès au stagiaire via la relation Eloquent (évite les doublons)
                $stagiaire->achievements()->attach($achievement->id, [
                    'unlocked_at' => $now,
                ]);
                $newAchievements[] = $achievement;
            }
        }
        // Mettre à jour la propriété unlockedAchievements (optionnel)
        $stagiaire->unlockedAchievements = $stagiaire->achievements()->get();
        return $newAchievements;
    }
}
