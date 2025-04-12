<?php

namespace App\Services;

use App\Repositories\Interfaces\RankingRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;

class RankingService
{
    protected $rankingRepository;
    protected $quizRepository;

    public function __construct(
        RankingRepositoryInterface $rankingRepository,
        QuizRepositoryInterface $quizRepository
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
        // Logique de calcul du niveau basée sur les points
        $levels = [
            0 => 'Débutant',
            100 => 'Intermédiaire',
            300 => 'Avancé',
            600 => 'Expert',
            1000 => 'Maître'
        ];

        $level = 'Débutant';
        foreach ($levels as $threshold => $name) {
            if ($points >= $threshold) {
                $level = $name;
            }
        }

        return $level;
    }
} 