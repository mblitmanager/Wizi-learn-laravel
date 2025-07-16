<?php
namespace App\Services;

use App\Models\Stagiaire;
use App\Models\Achievement;
use App\Models\UserAchievement;
use Carbon\Carbon;

class StagiaireAchievementService
{
    /**
     * Vérifie et met à jour les succès du stagiaire
     * @param Stagiaire $stagiaire
     * @return array Liste des succès nouvellement débloqués
     */
    public function checkAchievements(Stagiaire $stagiaire)
    {
        $newAchievements = [];
        $now = Carbon::now();

        // Série de connexions journalières
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
        $stagiaire->login_streak = $streak;
        $stagiaire->last_login_at = $now;
        $stagiaire->save();

        // Points totaux
        $totalPoints = $stagiaire->classements()->sum('points');

        // Palier atteint
        $level = 'bronze';
        if ($totalPoints >= 3000) {
            $level = 'gold';
        } elseif ($totalPoints >= 1500) {
            $level = 'silver';
        }

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
            }
            if ($unlocked && !in_array($achievement->id, $alreadyUnlocked)) {
                $stagiaire->achievements()->attach($achievement->id, ['unlocked_at' => $now]);
                $newAchievements[] = $achievement;
            }
        }
        $stagiaire->unlockedAchievements = $stagiaire->achievements()->get();
        return $newAchievements;
    }
}
