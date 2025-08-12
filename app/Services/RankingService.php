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

        // Récupérer les participations du stagiaire
        $userId = \App\Models\Stagiaire::where('id', $stagiaireId)->value('user_id');
        $participations = \App\Models\QuizParticipation::where('user_id', $userId)->get();

        $total_quizzes = $participations->count();

        $completed_quizzes = $participations->where('status', 'completed')->count();
        $average_score = $participations->avg('score');
        $total_points = $participations->sum('score');
        $total_time_spent = $participations->sum('time_spent');

        // Récupérer le rang et les points depuis Classement si besoin
        $classement = \App\Models\Classement::where('stagiaire_id', $stagiaireId)->first();
        $rang = $classement ? $classement->rang : null;

        $progress = [
            'total_quizzes' => $total_quizzes,
            'completed_quizzes' => $completed_quizzes,
            'average_score' => $average_score,
            'total_points' => $total_points,
            'total_time_spent' => $total_time_spent,
            'rang' => $rang,
            'level' => $this->calculateLevel($total_points)
        ];

        return $progress;
    }

    public function calculateLevel($points)
    {
        // Configuration des niveaux
        $basePoints = 10; // Points nécessaires pour chaque niveau
        $maxLevel = 100;    // Niveau maximum
        $levels = [];

        // Génération dynamique des niveaux
        for ($level = 0; $level <= $maxLevel; $level++) {
            $threshold = ($level - 1) * $basePoints;
            $levels[$threshold] = (string)$level;
        }

        $level = '0';
        foreach ($levels as $threshold => $name) {
            if ($points >= $threshold) {
                $level = $name;
            }
        }

        return $level;
    }
}
