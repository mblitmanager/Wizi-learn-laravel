<?php

namespace App\Services;

use App\Repositories\RankingRepository;
use App\Repositories\QuizeRepository;

class RankingService
{
    protected $rankingRepository;
    protected $quizRepository;

    public function __construct(
        RankingRepository $rankingRepository,
        QuizeRepository $quizRepository
    ) {
        $this->rankingRepository = $rankingRepository;
        $this->quizRepository = $quizRepository;
    }

    public function getGlobalRanking()
    {
        return $this->rankingRepository->getGlobalRanking();
    }

    public function getFormationRanking($formationId)
    {
        return $this->rankingRepository->getFormationRanking($formationId);
    }

    public function getStagiaireRewards($stagiaireId)
    {
        return $this->rankingRepository->getStagiaireRewards($stagiaireId);
    }

    public function getStagiaireProgress($stagiaireId)
    {
        $quizzes = $this->quizRepository->getQuizzesByStagiaire($stagiaireId);
        $progress = [
            'total_quizzes' => $quizzes->count(),
            'completed_quizzes' => $quizzes->where('completed', true)->count(),
            'average_score' => $quizzes->avg('score'),
            'total_points' => $quizzes->sum('points'),
            'level' => $this->calculateLevel($quizzes->sum('points'))
        ];

        return $progress;
    }

    private function calculateLevel($points)
    {
        // Configuration des niveaux
        $basePoints = 100; // Points nécessaires pour chaque niveau
        $maxLevel = 100;    // Niveau maximum
        $levels = [];

        // Génération dynamique des niveaux
        for ($level = 1; $level <= $maxLevel; $level++) {
            $threshold = ($level - 1) * $basePoints;
            $levels[$threshold] = (string)$level;
        }

        $level = '1';
        foreach ($levels as $threshold => $name) {
            if ($points >= $threshold) {
                $level = $name;
            }
        }

        return $level;
    }
}