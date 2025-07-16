<?php
namespace App\Services;

use App\Models\Stagiaire;
use App\Models\Achievement;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\DB;
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
        // ...existing code...
    }

    /**
     * Débloque un succès pour un stagiaire à partir d'un code unique (ex: 'android_download').
     * Retourne le succès débloqué ou [] si déjà débloqué ou code inconnu.
     */
    public function unlockAchievementByCode($stagiaire, $code)
    {
        $achievement = Achievement::where('code', $code)->first();
        if (!$achievement) {
            return [];
        }
        // Vérifie si déjà débloqué
        if ($stagiaire->achievements()->where('achievement_id', $achievement->id)->exists()) {
            return [];
        }
        $stagiaire->achievements()->attach($achievement->id, ['created_at' => now(), 'updated_at' => now()]);
        return [$achievement];
    }
}
