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

        // 2. Points totaux - Utiliser les progressions au lieu des classements
        $totalPoints = $stagiaire->progressions()->sum('score');

        // 3. Statistiques des quiz
        $quizStats = $this->getQuizStats($stagiaire);

        // 4. Statistiques des vidéos
        $videoStats = $this->getVideoStats($stagiaire);

        // 5. Statistiques des parrainages
        $referralStats = $this->getReferralStats($stagiaire);

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

                case 'points':
                    if ($totalPoints >= $achievement->condition) {
                        $unlocked = true;
                    }
                    break;

                case 'quiz':
                    // Premier quiz
                    if ($achievement->condition == 1 && $quizStats['total_quizzes'] >= 1) {
                        $unlocked = true;
                    }
                    break;

                case 'quiz_level':
                    if ($quizId && $achievement->condition == 1) {
                        // Vérifier si c'est le premier quiz de ce niveau
                        $quiz = \App\Models\Quiz::find($quizId);
                        if ($quiz && $quiz->niveau === $achievement->level) {
                            $unlocked = true;
                        }
                    }
                    break;

                case 'quiz_all':
                    if ($quizStats['total_quizzes'] >= $quizStats['available_quizzes']) {
                        $unlocked = true;
                    }
                    break;

                case 'quiz_all_level':
                    // Mapper les niveaux français vers les clés anglaises
                    $levelMapping = [
                        'débutant' => 'beginner',
                        'intermédiaire' => 'intermediate',
                        'avancé' => 'advanced'
                    ];
                    $levelKey = $levelMapping[$achievement->level] ?? $achievement->level;
                    if (isset($quizStats['quizzes_by_level'][$levelKey]) && 
                        isset($quizStats['available_by_level'][$levelKey]) &&
                        $quizStats['quizzes_by_level'][$levelKey] >= $quizStats['available_by_level'][$levelKey]) {
                        $unlocked = true;
                    }
                    break;

                case 'video':
                    if ($achievement->condition == 1 && $videoStats['total_videos'] >= 1) {
                        $unlocked = true;
                    } elseif ($achievement->condition == 0 && $videoStats['total_videos'] >= $videoStats['available_videos']) {
                        $unlocked = true;
                    }
                    break;

                case 'parrainage':
                    if ($referralStats['total_referrals'] >= $achievement->condition) {
                        $unlocked = true;
                    }
                    break;

                case 'action':
                    // Actions spécifiques (comme android_download) sont gérées par unlockAchievementByCode
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

        return $newAchievements;
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
        $stagiaire->achievements()->attach($achievement->id, [
            'unlocked_at' => now(),
        ]);
        return [$achievement];
    }

    /**
     * Récupère les statistiques des quiz pour un stagiaire
     */
    private function getQuizStats(Stagiaire $stagiaire)
    {
        // Utiliser les progressions au lieu des participations
        $progressions = $stagiaire->progressions()->with('quiz')->get();
        $totalQuizzes = $progressions->count();

        $quizzesByLevel = [
            'beginner' => $progressions->where('quiz.niveau', 'débutant')->count(),
            'intermediate' => $progressions->where('quiz.niveau', 'intermédiaire')->count(),
            'advanced' => $progressions->where('quiz.niveau', 'avancé')->count(),
        ];

        $availableQuizzes = \App\Models\Quiz::count();
        $availableByLevel = [
            'beginner' => \App\Models\Quiz::where('niveau', 'débutant')->count(),
            'intermediate' => \App\Models\Quiz::where('niveau', 'intermédiaire')->count(),
            'advanced' => \App\Models\Quiz::where('niveau', 'avancé')->count(),
        ];

        return [
            'total_quizzes' => $totalQuizzes,
            'quizzes_by_level' => $quizzesByLevel,
            'available_quizzes' => $availableQuizzes,
            'available_by_level' => $availableByLevel,
        ];
    }

    /**
     * Récupère les statistiques des vidéos pour un stagiaire
     */
    private function getVideoStats(Stagiaire $stagiaire)
    {
        $watchedVideos = $stagiaire->medias()->where('is_watched', true)->count();
        $availableVideos = \App\Models\Media::count();

        return [
            'total_videos' => $watchedVideos,
            'available_videos' => $availableVideos,
        ];
    }

    /**
     * Récupère les statistiques des parrainages pour un stagiaire
     */
    private function getReferralStats(Stagiaire $stagiaire)
    {
        $totalReferrals = $stagiaire->referrals()->count();

        return [
            'total_referrals' => $totalReferrals,
        ];
    }
}
