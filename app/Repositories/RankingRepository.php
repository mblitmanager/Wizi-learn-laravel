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
        $progress = Progression::where('stagiaire_id', $userId)
            ->select('points', 'completed_quizzes', 'completed_challenges')
            ->first();

        if (!$progress) {
            return [
                'points' => 0,
                'completed_quizzes' => 0,
                'completed_challenges' => 0,
                'rank' => $this->calculateUserRank($userId),
            ];
        }

        return array_merge($progress->toArray(), [
            'rank' => $this->calculateUserRank($userId),
        ]);
    }

    /**
     * Get the global ranking of all users
     *
     * @param int $limit
     * @return array
     */
    public function getGlobalRanking(int $limit = 10): array
    {
        return DB::table('progressions')
            ->join('users', 'progressions.stagiaire_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'progressions.points')
            ->orderBy('progressions.points', 'desc')
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
        $userPoints = Progression::where('stagiaire_id', $userId)
            ->value('points') ?? 0;

        return DB::table('progressions')
            ->where('points', '>', $userPoints)
            ->count() + 1;
    }

    /**
     * Get the ranking for a specific formation
     *
     * @param int $formationId
     * @return array
     */
    public function getFormationRanking(int $formationId): array
    {
        return DB::table('progressions')
            ->join('users', 'progressions.stagiaire_id', '=', 'users.id')
            ->where('progressions.formation_id', $formationId)
            ->select('users.id', 'users.name', 'progressions.points')
            ->orderBy('progressions.points', 'desc')
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
        $progress = Progression::where('stagiaire_id', $stagiaireId)
            ->select('points')
            ->first();

        if (!$progress) {
            return [
                'points' => 0,
                'completed_quizzes' => 0,
                'completed_challenges' => 0
            ];
        }

        // Calculer le nombre de quizzes complétés depuis participations
        $completedQuizzes = DB::table('participations')
            ->where('stagiaire_id', $stagiaireId)
            ->where('status', 'completed')
            ->count();

        // Pour l'instant, on met completed_challenges à 0 car on n'a pas encore implémenté cette fonctionnalité
        $completedChallenges = 0;

        return [
            'points' => $progress->points,
            'completed_quizzes' => $completedQuizzes,
            'completed_challenges' => $completedChallenges
        ];
    }
}
