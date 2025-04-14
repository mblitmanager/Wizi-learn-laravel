<?php

namespace App\Repositories\Interfaces;

interface RankingRepositoryInterface
{
    /**
     * Get the progress/ranking for a specific user
     *
     * @param int $userId
     * @return array
     */
    public function getUserProgress(int $userId): array;

    /**
     * Get the global ranking of all users
     *
     * @param int $limit
     * @return array
     */
    public function getGlobalRanking(int $limit = 10): array;

    /**
     * Update the progress/ranking for a specific user
     *
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateUserProgress(int $userId, array $data): bool;
}
