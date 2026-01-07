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

    /**
     * Get optimized home data - consolidates multiple endpoints
     */
    /**
     * Get optimized home data - consolidates multiple endpoints
     */
    public function getHomeData($stagiaireId)
    {
        $stagiaire = \App\Models\Stagiaire::with([
            'user', 
            'formateurs.user', 
            'commercials.user', 
            'poleRelationClients.user'
        ])->find($stagiaireId);
        
        if (!$stagiaire) {
            throw new \Exception('Stagiaire not found');
        }

        // 1. Get basic quiz stats (NOT full quiz list)
        $quizStats = \App\Models\Classement::where('stagiaire_id', $stagiaireId)
            ->selectRaw('COUNT(*) as total_quizzes, SUM(points) as total_points, AVG(points) as avg_score')
            ->first();

        // 2. Get last 3 quiz history entries (NOT full history)
        $recentHistory = \App\Models\Classement::where('stagiaire_id', $stagiaireId)
            ->with(['quiz' => function($q) {
                $q->select(['id', 'titre', 'niveau']);
            }])
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get(['id', 'quiz_id', 'points', 'updated_at']);

        // 3. Get contacts summary (lightweight)
        $mapContact = function($contact) {
            return [
                'id' => $contact->id,
                'prenom' => $contact->prenom,
                'nom' => $contact->user->name ?? $contact->nom ?? '',
                'email' => $contact->user->email ?? $contact->email ?? null,
                'telephone' => $contact->telephone ?? null
            ];
        };

        $formateurs = $stagiaire->formateurs->map($mapContact);
        $commerciaux = $stagiaire->commercials->map($mapContact);
        $poleRelation = $stagiaire->poleRelationClients->map($mapContact);

        // 4. Get top 3 catalogue formations (lightweight)
        $catalogueFormations = \App\Models\CatalogueFormation::where('statut', 1)
            ->with(['formation' => function($q) {
                $q->where('statut', 1)
                  ->select(['id', 'titre', 'categorie']);
            }])
            ->select(['id', 'titre', 'duree', 'image_url', 'formation_id'])
            ->take(3)
            ->get();

        // 5. Get quiz categories (lightweight)
        $categories = \App\Models\Formation::where('statut', 1)
            ->select('categorie')
            ->distinct()
            ->pluck('categorie');

        return [
            'user' => [
                'id' => $stagiaire->id,
                'prenom' => $stagiaire->prenom,
                'image' => $stagiaire->user->image ?? null
            ],
            'quiz_stats' => [
                'total_quizzes' => $quizStats->total_quizzes ?? 0,
                'total_points' => $quizStats->total_points ?? 0,
                'average_score' => round($quizStats->avg_score ?? 0, 2)
            ],
            'recent_history' => $recentHistory,
            'contacts' => [
                'formateurs' => $formateurs,
                'commerciaux' => $commerciaux,
                'pole_relation' => $poleRelation
            ],
            'catalogue_formations' => $catalogueFormations,
            'categories' => $categories
        ];
    }
} 