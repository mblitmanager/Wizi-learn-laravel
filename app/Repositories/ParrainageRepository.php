<?php

namespace App\Repositories;

use App\Models\Parainage;
use App\Repositories\Interfaces\ParrainageRepositoryInterface;

class ParrainageRepository implements ParrainageRepositoryInterface
{
    /**
     * Get all filleuls (sponsored users) for a given parrain (sponsor)
     *
     * @param int $parrainId
     * @return array
     */
    public function getFilleuls(int $parrainId): array
    {
        return Parainage::with('filleul')
            ->where('parrain_id', $parrainId)
            ->get()
            ->map(function ($parainage) {
                return [
                    'id' => $parainage->filleul->id,
                    'name' => $parainage->filleul->name,
                    'email' => $parainage->filleul->email,
                    'created_at' => $parainage->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * Get parrain (sponsor) information for a given filleul (sponsored user)
     *
     * @param int $filleulId
     * @return array|null
     */
    public function getParrain(int $filleulId): ?array
    {
        $parainage = Parainage::with('parrain')
            ->where('filleul_id', $filleulId)
            ->first();

        if (!$parainage) {
            return null;
        }

        return [
            'id' => $parainage->parrain->id,
            'name' => $parainage->parrain->name,
            'email' => $parainage->parrain->email,
            'created_at' => $parainage->created_at,
        ];
    }

    /**
     * Create a new sponsorship relationship
     *
     * @param int $parrainId
     * @param int $filleulId
     * @return bool
     */
    public function createParrainage(int $parrainId, int $filleulId): bool
    {
        return Parainage::create([
            'parrain_id' => $parrainId,
            'filleul_id' => $filleulId,
        ]) !== null;
    }
}
