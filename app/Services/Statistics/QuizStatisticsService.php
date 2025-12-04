<?php

namespace App\Services\Statistics;

use App\Models\Quiz;
use App\Models\ResultatQuiz;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuizStatisticsService
{
    /**
     * Get total number of quizzes
     */
    public function getTotalQuizzes(): int
    {
        return Quiz::count();
    }

    /**
     * Get global success rate
     */
    public function getGlobalSuccessRate(string $period = '30d'): float
    {
        $date = $this->getPeriodDate($period);
        
        $total = ResultatQuiz::when($date, fn($q) => $q->where('created_at', '>=', $date))->count();
        $passed = ResultatQuiz::when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->where('reussi', true)
            ->count();

        return $total > 0 ? round(($passed / $total) * 100, 2) : 0;
    }

    /**
     * Get average score
     */
    public function getAverageScore(string $period = '30d'): float
    {
        $date = $this->getPeriodDate($period);
        
        return ResultatQuiz::when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->avg('score') ?? 0;
    }

    /**
     * Get average completion time
     */
    public function getAverageCompletionTime(string $period = '30d'): string
    {
        $date = $this->getPeriodDate($period);
        
        $avgSeconds = ResultatQuiz::when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->whereNotNull('temps_total')
            ->avg('temps_total') ?? 0;

        $minutes = floor($avgSeconds / 60);
        $seconds = $avgSeconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get total quiz attempts
     */
    public function getTotalAttempts(string $period = '30d'): int
    {
        $date = $this->getPeriodDate($period);
        
        return ResultatQuiz::when($date, fn($q) => $q->where('created_at', '>=', $date))->count();
    }

    /**
     * Get hardest questions (lowest success rate)
     */
    public function getHardestQuestions(int $limit = 10, string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        
        return DB::table('questions')
            ->join('reponses', 'questions.id', '=', 'reponses.question_id')
            ->join('resultat_quiz', 'reponses.resultat_quiz_id', '=', 'resultat_quiz.id')
            ->when($date, fn($q) => $q->where('resultat_quiz.created_at', '>=', $date))
            ->select(
                'questions.id',
                'questions.intitule_question as text',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN reponses.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('ROUND((SUM(CASE WHEN reponses.is_correct = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as success_rate')
            )
            ->groupBy('questions.id', 'questions.intitule_question')
            ->orderBy('success_rate', 'asc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get easiest questions (highest success rate)
     */
    public function getEasiestQuestions(int $limit = 10, string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        
        return DB::table('questions')
            ->join('reponses', 'questions.id', '=', 'reponses.question_id')
            ->join('resultat_quiz', 'reponses.resultat_quiz_id', '=', 'resultat_quiz.id')
            ->when($date, fn($q) => $q->where('resultat_quiz.created_at', '>=', $date))
            ->select(
                'questions.id',
                'questions.intitule_question as text',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN reponses.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('ROUND((SUM(CASE WHEN reponses.is_correct = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as success_rate')
            )
            ->groupBy('questions.id', 'questions.intitule_question')
            ->orderBy('success_rate', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get score distribution
     */
    public function getScoreDistribution(string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        
        return ResultatQuiz::when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->select(
                DB::raw('FLOOR(score / 10) * 10 as score_range'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('score_range')
            ->orderBy('score_range')
            ->get()
            ->map(function ($item) {
                return [
                    'range' => $item->score_range . '-' . ($item->score_range + 9),
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get trends over time
     */
    public function getTrendsOverTime(string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        $groupBy = $this->getGroupByFormat($period);
        
        return ResultatQuiz::when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->select(
                DB::raw($groupBy . ' as date'),
                DB::raw('COUNT(*) as attempts'),
                DB::raw('AVG(score) as avg_score'),
                DB::raw('SUM(CASE WHEN reussi = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as success_rate')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Helper: Get date from period string
     */
    private function getPeriodDate(string $period): ?Carbon
    {
        return match ($period) {
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            '90d' => Carbon::now()->subDays(90),
            '1y' => Carbon::now()->subYear(),
            default => null,
        };
    }

    /**
     * Helper: Get SQL group by format based on period
     */
    private function getGroupByFormat(string $period): string
    {
        return match ($period) {
            '7d' => "DATE_FORMAT(created_at, '%Y-%m-%d')",
            '30d' => "DATE_FORMAT(created_at, '%Y-%m-%d')",
            '90d' => "DATE_FORMAT(created_at, '%Y-%W')",
            '1y' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => "DATE_FORMAT(created_at, '%Y-%m-%d')",
        };
    }
}
