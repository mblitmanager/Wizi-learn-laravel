<?php

namespace App\Services;

use App\Repositories\Interfaces\ParrainageRepositoryInterface;
use Illuminate\Support\Str;

class ParrainageService
{
    protected $parrainageRepository;

    public function __construct(ParrainageRepositoryInterface $parrainageRepository)
    {
        $this->parrainageRepository = $parrainageRepository;
    }

    public function getParrainageLink($stagiaireId)
    {
        $parrainage = $this->parrainageRepository->findByStagiaireId($stagiaireId);

        if (!$parrainage) {
            $parrainage = $this->parrainageRepository->create([
                'stagiaire_id' => $stagiaireId,
                'code' => Str::random(8),
                'link' => config('app.url') . '/register?ref=' . Str::random(8)
            ]);
        }

        return $parrainage->link;
    }

    public function getFilleuls($stagiaireId)
    {
        return $this->parrainageRepository->getFilleuls($stagiaireId);
    }

    public function getParrainageStats($stagiaireId)
    {
        $filleuls = $this->getFilleuls($stagiaireId);

        return [
            'total_filleuls' => $filleuls->count(),
            'active_filleuls' => $filleuls->where('active', true)->count(),
            'total_rewards' => $filleuls->sum('reward_points'),
            'filleuls_by_formation' => $filleuls->groupBy('formation_id')->map->count(),
            'recent_filleuls' => $filleuls->sortByDesc('created_at')->take(5)
        ];
    }
}
