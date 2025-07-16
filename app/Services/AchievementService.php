<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    /**
     * Vérifie et met à jour les succès de l'utilisateur
     * @param User $user
     * @return array Liste des succès nouvellement débloqués
     */
    public function checkAchievements(User $user)
    {
        $newAchievements = [];
        $now = Carbon::now();

        // 1. Série de connexions journalières
        $lastLogin = $user->last_login_at ? Carbon::parse($user->last_login_at) : null;
        $streak = $user->login_streak ?? 0;
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
        $user->login_streak = $streak;
        $user->last_login_at = $now;
        $user->save();

        // 2. Points totaux
        $totalPoints = $user->classements()->sum('points');

        // 3. Palier atteint
        $level = 'bronze';
        if ($totalPoints >= 3000) {
            $level = 'gold';
        } elseif ($totalPoints >= 1500) {
            $level = 'silver';
        }

        // Récupérer tous les succès
        $achievements = Achievement::all();
        $alreadyUnlocked = $user->achievements->pluck('id')->toArray();

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
            }
            if ($unlocked && !in_array($achievement->id, $alreadyUnlocked)) {
                UserAchievement::create([
                    'user_id' => $user->id,
                    'achievement_id' => $achievement->id,
                    'unlocked_at' => $now,
                ]);
                $newAchievements[] = $achievement;
            }
        }
        // Mettre à jour la propriété unlockedAchievements (optionnel)
        $user->unlockedAchievements = $user->achievements()->get();
        return $newAchievements;
    }
}
