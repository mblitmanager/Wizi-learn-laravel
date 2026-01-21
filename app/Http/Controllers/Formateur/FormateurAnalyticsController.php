<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Quiz;
use App\Models\QuizParticipation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormateurAnalyticsController extends Controller
{
    /**
     * Vérifier que l'utilisateur est un formateur
     */
    private function checkFormateur()
    {
        $user = Auth::user();
        if ($user->role !== 'formateur' && $user->role !== 'formatrice') {
            abort(403, 'Accès réservé aux formateurs.');
        }

        if (!$user->formateur) {
            abort(404, 'Profil formateur non trouvé.');
        }
    }

    /**
     * API: Get quiz success rate analytics
     * GET /formateur/analytics/quiz-success-rate
     */
    public function getQuizSuccessRate(Request $request)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $period = $request->get('period', 30); // days

        // Get stagiaires for this formateur
        $userIds = $formateur->stagiaires()->pluck('stagiaires.user_id');

        // Get quiz participations with success rates
        $quizStats = QuizParticipation::whereIn('user_id', $userIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->with('quiz')
            ->get()
            ->groupBy('quiz_id')
            ->map(function ($participations) {
                $total = $participations->count();
                $quiz = $participations->first()->quiz;
                
                // Calculate success (score >= 50%)
                $successful = $participations->filter(function ($p) use ($quiz) {
                    $maxScore = $quiz->nb_points_total ?? 100;
                    return ($p->score / $maxScore) >= 0.5;
                })->count();

                $successRate = $total > 0 ? round(($successful / $total) * 100, 1) : 0;

                return [
                    'quiz_id' => $quiz->id,
                    'quiz_name' => $quiz->nom,
                    'category' => $quiz->categorie ?? 'Général',
                    'total_attempts' => $total,
                    'successful_attempts' => $successful,
                    'success_rate' => $successRate,
                    'average_score' => round($participations->avg('score'), 1),
                ];
            })
            ->values();

        return response()->json([
            'period_days' => $period,
            'quiz_stats' => $quizStats,
        ]);
    }

    /**
     * API: Get completion time analytics
     * GET /formateur/analytics/completion-time
     */
    public function getCompletionTime(Request $request)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $period = $request->get('period', 30);
        $userIds = $formateur->stagiaires()->pluck('stagiaires.user_id');

        // Get average completion time per quiz over time
        $completionTrends = QuizParticipation::whereIn('user_id', $userIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(time_spent) as avg_time'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'avg_time_minutes' => round($item->avg_time / 60, 1),
                    'quiz_count' => $item->count,
                ];
            });

        // Get average time per quiz
        $quizAvgTimes = QuizParticipation::whereIn('user_id', $userIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->with('quiz')
            ->get()
            ->groupBy('quiz_id')
            ->map(function ($participations) {
                $quiz = $participations->first()->quiz;
                return [
                    'quiz_name' => $quiz->nom,
                    'category' => $quiz->categorie ?? 'Général',
                    'avg_time_minutes' => round($participations->avg('time_spent') / 60, 1),
                    'attempts' => $participations->count(),
                ];
            })
            ->values();

        return response()->json([
            'period_days' => $period,
            'completion_trends' => $completionTrends,
            'quiz_avg_times' => $quizAvgTimes,
        ]);
    }

    /**
     * API: Get activity heatmap (which days/hours students are active)
     * GET /formateur/analytics/activity-heatmap
     */
    public function getActivityHeatmap(Request $request)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $period = $request->get('period', 30);
        $userIds = $formateur->stagiaires()->pluck('stagiaires.user_id');

        // Get activity by day of week and hour
        $activityByDay = QuizParticipation::whereIn('user_id', $userIds)
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->select(
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('COUNT(*) as activity_count')
            )
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get()
            ->map(function ($item) {
                $days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
                return [
                    'day' => $days[$item->day_of_week - 1] ?? 'Unknown',
                    'activity_count' => $item->activity_count,
                ];
            });

        $activityByHour = QuizParticipation::whereIn('user_id', $userIds)
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as activity_count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => $item->hour,
                    'activity_count' => $item->activity_count,
                ];
            });

        return response()->json([
            'period_days' => $period,
            'activity_by_day' => $activityByDay,
            'activity_by_hour' => $activityByHour,
        ]);
    }

    /**
     * API: Get dropout rate (where students abandon formations/quizzes)
     * GET /formateur/analytics/dropout-rate
     */
    public function getDropoutRate(Request $request)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $userIds = $formateur->stagiaires()->pluck('stagiaires.user_id');

        // Quiz abandonment (started but not completed)
        $quizDropout = QuizParticipation::whereIn('user_id', $userIds)
            ->select(
                'quiz_id',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status != "completed" THEN 1 ELSE 0 END) as abandoned')
            )
            ->with('quiz')
            ->groupBy('quiz_id')
            ->get()
            ->map(function ($item) {
                $dropoutRate = $item->total_attempts > 0
                    ? round(($item->abandoned / $item->total_attempts) * 100, 1)
                    : 0;

                return [
                    'quiz_name' => $item->quiz->nom ?? 'Unknown',
                    'category' => $item->quiz->categorie ?? 'Général',
                    'total_attempts' => $item->total_attempts,
                    'completed' => $item->completed,
                    'abandoned' => $item->abandoned,
                    'dropout_rate' => $dropoutRate,
                ];
            })
            ->sortByDesc('dropout_rate')
            ->values();

        // Overall stats
        $totalAttempts = $quizDropout->sum('total_attempts');
        $totalCompleted = $quizDropout->sum('completed');
        $totalAbandoned = $quizDropout->sum('abandoned');
        $overallDropoutRate = $totalAttempts > 0
            ? round(($totalAbandoned / $totalAttempts) * 100, 1)
            : 0;

        return response()->json([
            'overall' => [
                'total_attempts' => $totalAttempts,
                'completed' => $totalCompleted,
                'abandoned' => $totalAbandoned,
                'dropout_rate' => $overallDropoutRate,
            ],
            'quiz_dropout' => $quizDropout,
        ]);
    }

    /**
     * API: Get comprehensive dashboard stats
     * GET /formateur/analytics/dashboard
     */
    public function getDashboard(Request $request)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $period = $request->get('period', 30);
        $userIds = $formateur->stagiaires()->pluck('stagiaires.user_id');

        // Total stagiaires
        $totalStagiaires = $userIds->count();

        // Active stagiaires (participated in last 7 days)
        $activeStagiaires = QuizParticipation::whereIn('user_id', $userIds)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->distinct('stagiaire_id')
            ->count('stagiaire_id');

        // Total quiz completions
        $totalCompletions = QuizParticipation::whereIn('user_id', $userIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->count();

        // Average score
        $avgScore = QuizParticipation::whereIn('user_id', $userIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->avg('score');

        // Trend (compare with previous period)
        $previousCompletions = QuizParticipation::whereIn('user_id', $userIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays($period * 2))
            ->where('created_at', '<', Carbon::now()->subDays($period))
            ->count();

        $trend = $previousCompletions > 0
            ? round((($totalCompletions - $previousCompletions) / $previousCompletions) * 100, 1)
            : 0;

        return response()->json([
            'period_days' => $period,
            'summary' => [
                'total_stagiaires' => $totalStagiaires,
                'active_stagiaires' => $activeStagiaires,
                'total_completions' => $totalCompletions,
                'average_score' => round($avgScore, 1),
                'trend_percentage' => $trend,
            ],
        ]);
    }

    /**
     * API: Get performance stats for all formations belonging to the formateur
     * GET /formateur/analytics/formations/performance
     */
    public function getFormationsPerformance(Request $request)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        // Get formations assigned to this formateur
        $formations = $formateur->catalogue_formations()
            ->withCount('stagiaires as student_count')
            ->get();

        $performance = $formations->map(function ($formation) use ($formateur) {
            // Get stagiaire user IDs for this formation
            $stagiaireUserIds = $formation->stagiaires()->pluck('user_id');

            // Aggregate results for quizzes belonging to this formation's base formation
            $stats = DB::table('quiz_participations')
                ->join('quizzes', 'quiz_participations.quiz_id', '=', 'quizzes.id')
                ->where('quizzes.formation_id', $formation->formation_id)
                ->whereIn('quiz_participations.user_id', $stagiaireUserIds)
                ->select([
                    DB::raw('AVG(score) as avg_score'),
                    DB::raw('COUNT(CASE WHEN quiz_participations.status = "completed" THEN 1 END) as total_completions')
                ])
                ->first();

            return [
                'id' => $formation->id,
                'titre' => $formation->titre,
                'image_url' => $formation->image_url,
                'tarif' => $formation->tarif,
                'student_count' => (int) $formation->student_count,
                'avg_score' => round($stats->avg_score ?? 0, 1),
                'total_completions' => (int) ($stats->total_completions ?? 0),
            ];
        });

        return response()->json($performance);
    }
}
