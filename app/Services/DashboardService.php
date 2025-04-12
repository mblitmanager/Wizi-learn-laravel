<?php

namespace App\Services;

use App\Repositories\Interfaces\QuizRepositoryInterface;
use App\Repositories\Interfaces\FormationRepositoryInterface;
use App\Repositories\Interfaces\StagiaireRepositoryInterface;

class DashboardService
{
    protected $quizRepository;
    protected $formationRepository;
    protected $stagiaireRepository;

    public function __construct(
        QuizRepositoryInterface $quizRepository,
        FormationRepositoryInterface $formationRepository,
        StagiaireRepositoryInterface $stagiaireRepository
    ) {
        $this->quizRepository = $quizRepository;
        $this->formationRepository = $formationRepository;
        $this->stagiaireRepository = $stagiaireRepository;
    }

    public function getStagiaireDashboard($stagiaireId)
    {
        return [
            'quiz_stats' => $this->getQuizStatistics($stagiaireId),
            'formation_stats' => $this->getFormationStatistics($stagiaireId),
            'comparison_stats' => $this->getComparisonStatistics($stagiaireId),
            'recent_activity' => $this->getRecentActivity($stagiaireId)
        ];
    }

    public function getQuizStatistics($stagiaireId)
    {
        $quizzes = $this->quizRepository->getQuizzesByStagiaire($stagiaireId);
        
        return [
            'total_quizzes' => $quizzes->count(),
            'completed_quizzes' => $quizzes->where('completed', true)->count(),
            'average_score' => $quizzes->avg('score'),
            'best_score' => $quizzes->max('score'),
            'quizzes_by_level' => $quizzes->groupBy('level')->map->count(),
            'quizzes_by_category' => $quizzes->groupBy('category')->map->count()
        ];
    }

    public function getFormationStatistics($stagiaireId)
    {
        $formations = $this->formationRepository->getFormationsByStagiaire($stagiaireId);
        
        return [
            'total_formations' => $formations->count(),
            'completed_formations' => $formations->where('completed', true)->count(),
            'in_progress_formations' => $formations->where('in_progress', true)->count(),
            'average_progress' => $formations->avg('progress'),
            'formations_by_category' => $formations->groupBy('category')->map->count()
        ];
    }

    public function getComparisonStatistics($stagiaireId)
    {
        $allStagiaires = $this->stagiaireRepository->all();
        $currentStagiaire = $this->stagiaireRepository->find($stagiaireId);
        
        return [
            'global_ranking' => $this->calculateGlobalRanking($currentStagiaire, $allStagiaires),
            'category_rankings' => $this->calculateCategoryRankings($currentStagiaire, $allStagiaires),
            'performance_comparison' => $this->comparePerformance($currentStagiaire, $allStagiaires)
        ];
    }

    private function getRecentActivity($stagiaireId)
    {
        $quizzes = $this->quizRepository->getQuizzesByStagiaire($stagiaireId)
            ->sortByDesc('completed_at')
            ->take(5);
            
        $formations = $this->formationRepository->getFormationsByStagiaire($stagiaireId)
            ->sortByDesc('last_activity')
            ->take(5);

        return [
            'recent_quizzes' => $quizzes,
            'recent_formations' => $formations
        ];
    }

    private function calculateGlobalRanking($currentStagiaire, $allStagiaires)
    {
        // Implémentation de la logique de classement global
        return [
            'position' => 1, // À implémenter
            'total_stagiaires' => $allStagiaires->count()
        ];
    }

    private function calculateCategoryRankings($currentStagiaire, $allStagiaires)
    {
        // Implémentation de la logique de classement par catégorie
        return [];
    }

    private function comparePerformance($currentStagiaire, $allStagiaires)
    {
        // Implémentation de la comparaison des performances
        return [
            'average_score' => 0, // À implémenter
            'percentile' => 0 // À implémenter
        ];
    }
} 