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
        return Parainage::with(['filleul.user', 'filleul.formations'])
            ->where('parrain_id', $parrainId)
            ->get()
            ->filter(function ($parainage) {
                return $parainage->filleul !== null;
            })
            ->map(function ($parainage) {
                return [
                    'id' => $parainage->filleul->id,
                    'name' => $parainage->filleul->user->name,
                    'email' => $parainage->filleul->user->email,
                    'telephone' => $parainage->filleul->telephone,
                    'ville' => $parainage->filleul->ville,
                    'formations' => $parainage->filleul->formations->map(function ($formation) {
                        return [
                            'id' => $formation->id,
                            'name' => $formation->name,
                            'description' => $formation->description
                        ];
                    }),
                    'created_at' => $parainage->created_at,
                    'accepted_at' => $parainage->accepted_at
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

    /**
     * Find parrainage by stagiaire ID
     *
     * @param int $stagiaireId
     * @return mixed
     */
    public function findByStagiaireId(int $stagiaireId)
    {
        return Parainage::where('parrain_id', $stagiaireId)->first();
    }

    /**
     * Create a new parrainage
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Parainage::create($data);
    }

    public function addPoints(int $stagiaireId, int $points): void
    {
        $parrainage = $this->findByStagiaireId($stagiaireId);
        if ($parrainage) {
            $parrainage->points += $points;
            $parrainage->save();
        }
    }

    public function findByToken(string $token)
    {
        return Parainage::where('token', $token)->first();
    }

    public function getHistory(int $stagiaireId): array
    {
        return Parainage::where('parrain_id', $stagiaireId)
            ->with('filleul')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getRewards(int $stagiaireId): array
    {
        return Parainage::where('parrain_id', $stagiaireId)
            ->where('points', '>', 0)
            ->get()
            ->toArray();
    }

    public function update(int $id, array $data): bool
    {
        return Parainage::where('id', $id)->update($data) > 0;
    }
}
