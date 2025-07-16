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

        // Récupérer les stats quiz du stagiaire
        $progressions = $stagiaire->progressions()->with('quiz')->get();
        $quizzesDone = [];
        $perfectQuizzes = [];
        $today = now()->startOfDay();
        $todayQuizzes = 0;
        $todayPerfect = 0;
        $levels = ['débutant', 'intermédiaire', 'avancé'];
        $quizzesByLevel = [];
        foreach ($progressions as $p) {
            if ($p->quiz) {
                $quizzesDone[] = $p->quiz_id;
                if ($p->score == $p->quiz->nb_points_total) {
                    $perfectQuizzes[] = $p->quiz_id;
                }
                if (\Carbon\Carbon::parse($p->created_at)->greaterThanOrEqualTo($today)) {
                    $todayQuizzes++;
                    if ($p->score == $p->quiz->nb_points_total) {
                        $todayPerfect++;
                    }
                }
                foreach ($levels as $lvl) {
                    if ($p->quiz->niveau === $lvl) {
                        if (!isset($quizzesByLevel[$lvl])) $quizzesByLevel[$lvl] = [];
                        $quizzesByLevel[$lvl][] = $p->quiz_id;
                    }
                }
            }
        }
        $quizzesDone = array_unique($quizzesDone);
        $perfectQuizzes = array_unique($perfectQuizzes);
        foreach ($levels as $lvl) {
            if (!isset($quizzesByLevel[$lvl])) $quizzesByLevel[$lvl] = [];
            $quizzesByLevel[$lvl] = array_unique($quizzesByLevel[$lvl]);
        }

        // Gestion des vidéos vues par le stagiaire (table dédiée)
        $videosVues = DB::table('stagiaire_videos')
            ->where('stagiaire_id', $stagiaire->id)
            ->pluck('media_id')
            ->toArray();
        $totalVideos = \App\Models\Media::where('categorie', 'tutoriel')->count();

        foreach ($achievements as $achievement) {
            $unlocked = false;
            switch ($achievement->type) {
                case 'connexion_serie':
                    if ($streak >= $achievement->condition) $unlocked = true;
                    break;
                case 'points_total':
                    if ($totalPoints >= $achievement->condition) $unlocked = true;
                    break;
                case 'palier':
                    if ($level === $achievement->level) $unlocked = true;
                    break;
                case 'quiz':
                    // Premier quiz
                    if ($achievement->condition == 1 && count($quizzesDone) >= 1) $unlocked = true;
                    // Tous les quiz d’un niveau
                    if ($achievement->level && isset($quizzesByLevel[$achievement->level])) {
                        $totalQuizzes = \App\Models\Quiz::where('niveau', $achievement->level)->count();
                        if ($totalQuizzes > 0 && count($quizzesByLevel[$achievement->level]) == $totalQuizzes) $unlocked = true;
                    }
                    // Tous les quiz
                    if ($achievement->name === 'Finir tous les quiz') {
                        $totalQuizzes = \App\Models\Quiz::count();
                        if ($totalQuizzes > 0 && count($quizzesDone) == $totalQuizzes) $unlocked = true;
                    }
                    break;
                case 'quiz_perfect':
                    // Quiz sans faute
                    if ($achievement->condition == 1 && count($perfectQuizzes) >= 1) $unlocked = true;
                    // Tous les quiz sans faute
                    $totalQuizzes = \App\Models\Quiz::count();
                    if ($achievement->name === 'Tous les quiz sans faute' && $totalQuizzes > 0 && count($perfectQuizzes) == $totalQuizzes) $unlocked = true;
                    break;
                case 'quiz_streak':
                    // Quiz successifs en une journée
                    if ($todayQuizzes >= $achievement->condition) $unlocked = true;
                    // Quiz perfect successifs en une journée
                    if ($achievement->name === 'Quiz perfect successifs' && $todayPerfect >= $achievement->condition) $unlocked = true;
                    break;
                case 'video':
                    // Première vidéo
                    if ($achievement->condition == 1 && count($videosVues) >= 1) $unlocked = true;
                    // Toutes les vidéos
                    if ($achievement->name === 'Toutes les vidéos' && $totalVideos > 0 && count($videosVues) == $totalVideos) $unlocked = true;
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
