<?php

namespace App\Repositories;

use App\Models\Progression;
use App\Repositories\Interfaces\RankingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RankingRepository implements RankingRepositoryInterface
{
    /**
     * Get the progress/ranking for a specific user
     *
     * @param int $userId
     * @return array
     */
    public function getUserProgress(int $userId): array
    {
        // Aggéger les meilleurs scores par quiz pour ce stagiaire
        $bestScores = Progression::where('stagiaire_id', $userId)
            ->select('quiz_id', DB::raw('MAX(score) as best_score'))
            ->groupBy('quiz_id')
            ->get();

        $points = $bestScores->sum('best_score');
        $completedQuizzes = $bestScores->count();
        $completedChallenges = 0; // Pas encore implémenté

        return [
            'points' => $points,
            'completed_quizzes' => $completedQuizzes,
            'completed_challenges' => $completedChallenges,
            'rank' => $this->calculateUserRank($userId),
        ];
    }

    /**
     * Get the global ranking of all users
     *
     * @param int $limit
     * @return array
     */
    public function getGlobalRanking(int $limit = 10): array
    {
        $subquery = DB::table('progressions')
            ->select('stagiaire_id', 'quiz_id', DB::raw('MAX(score) as best_score'))
            ->groupBy('stagiaire_id', 'quiz_id');

        return DB::table(DB::raw("({$subquery->toSql()}) as best_attempts"))
            ->mergeBindings($subquery)
            ->join('stagiaires', 'best_attempts.stagiaire_id', '=', 'stagiaires.id')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', DB::raw('SUM(best_attempts.best_score) as points'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('points', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Update the progress/ranking for a specific user
     *
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateUserProgress(int $userId, array $data): bool
    {
        return Progression::updateOrCreate(
            ['stagiaire_id' => $userId],
            $data
        ) !== null;
    }

    /**
     * Calculate the user's rank based on their points
     *
     * @param int $userId
     * @return int
     */
    private function calculateUserRank(int $userId): int
    {
        $subquery = DB::table('progressions')
            ->select('stagiaire_id', 'quiz_id', DB::raw('MAX(score) as best_score'))
            ->groupBy('stagiaire_id', 'quiz_id');

        $userPoints = DB::table(DB::raw("({$subquery->toSql()}) as best_attempts"))
            ->mergeBindings($subquery)
            ->where('stagiaire_id', $userId)
            ->sum('best_score');

        $rankings = DB::table(DB::raw("({$subquery->toSql()}) as best_attempts"))
            ->mergeBindings($subquery)
            ->select('stagiaire_id', DB::raw('SUM(best_attempts.best_score) as total_points'))
            ->groupBy('stagiaire_id')
            ->having('total_points', '>', $userPoints)
            ->get();

        return $rankings->count() + 1;
    }

    /**
     * Get the ranking for a specific formation
     *
     * @param int $formationId
     * @return array
     */
    public function getFormationRanking(int $formationId): array
    {
        $subquery = DB::table('progressions')
            ->where('formation_id', $formationId)
            ->select('stagiaire_id', 'quiz_id', DB::raw('MAX(score) as best_score'))
            ->groupBy('stagiaire_id', 'quiz_id');

        return DB::table(DB::raw("({$subquery->toSql()}) as best_attempts"))
            ->mergeBindings($subquery)
            ->join('stagiaires', 'best_attempts.stagiaire_id', '=', 'stagiaires.id')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', DB::raw('SUM(best_attempts.best_score) as points'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('points', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get the rewards for a specific stagiaire
     *
     * @param int $stagiaireId
     * @return array
     */
    public function getStagiaireRewards(int $stagiaireId): array
    {
        // Aggéger les meilleurs scores par quiz pour ce stagiaire
        $bestScores = Progression::where('stagiaire_id', $stagiaireId)
            ->select('quiz_id', DB::raw('MAX(score) as best_score'))
            ->groupBy('quiz_id')
            ->get();

        $points = $bestScores->sum('best_score');

        // Calculer le nombre de quizzes complétés (unique par quiz_id)
        $completedQuizzes = $bestScores->count();

        // Pour l'instant, on met completed_challenges à 0 car on n'a pas encore implémenté cette fonctionnalité
        $completedChallenges = 0;

        return [
            'points' => (int) $points,
            'completed_quizzes' => $completedQuizzes,
            'completed_challenges' => $completedChallenges
        ];
    }
}
