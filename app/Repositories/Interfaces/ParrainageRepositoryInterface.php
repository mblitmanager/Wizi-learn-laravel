<?php

namespace App\Repositories\Interfaces;

interface ParrainageRepositoryInterface
{
    /**
     * Get all filleuls (sponsored users) for a given parrain (sponsor)
     *
     * @param int $parrainId
     * @return array
     */
    public function getFilleuls(int $parrainId): array;

    /**
     * Get parrain (sponsor) information for a given filleul (sponsored user)
     *
     * @param int $filleulId
     * @return array|null
     */
    public function getParrain(int $filleulId): ?array;

    /**
     * Create a new sponsorship relationship
     *
     * @param int $parrainId
     * @param int $filleulId
     * @return bool
     */
    public function createParrainage(int $parrainId, int $filleulId): bool;
}
