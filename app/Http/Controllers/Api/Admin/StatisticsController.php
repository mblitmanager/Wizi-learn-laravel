<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Statistics\QuizStatisticsService;
use App\Services\Statistics\FormationStatisticsService;
use App\Services\Statistics\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    protected $quizStats;
    protected $formationStats;
    protected $userActivity;

    public function __construct(
        QuizStatisticsService $quizStats,
        FormationStatisticsService $formationStats,
        UserActivityService $userActivity
    ) {
        $this->quizStats = $quizStats;
        $this->formationStats = $formationStats;
        $this->userActivity = $userActivity;
        $this->middleware('auth:api');
        $this->middleware('role:admin');
    }

    /**
     * Get dashboard overview statistics
     */
    public function dashboard(): JsonResponse
    {
        try {
            $data = [
                'summary' => [
                    'totalStudents' => $this->userActivity->getTotalStudents(),
                    'activeStudents' => $this->userActivity->getActiveStudents(),
                    'totalFormations' => $this->formationStats->getTotalFormations(),
                    'averageCompletionRate' => $this->formationStats->getAverageCompletionRate(),
                ],
                'onlineUsers' => $this->userActivity->getOnlineUsersCount(),
                'recentActivity' => $this->userActivity->getRecentActivity(10),
                'topFormations' => $this->formationStats->getTopFormations(5),
                'quizOverview' => [
                    'totalQuizzes' => $this->quizStats->getTotalQuizzes(),
                    'averageScore' => $this->quizStats->getAverageScore(),
                    'successRate' => $this->quizStats->getGlobalSuccessRate(),
                ],
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch dashboard statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quiz statistics
     */
    public function quizStats(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30d');
            
            $data = [
                'globalSuccessRate' => $this->quizStats->getGlobalSuccessRate($period),
                'averageScore' => $this->quizStats->getAverageScore($period),
                'averageTime' => $this->quizStats->getAverageCompletionTime($period),
                'totalAttempts' => $this->quizStats->getTotalAttempts($period),
                'hardestQuestions' => $this->quizStats->getHardestQuestions(10, $period),
                'easiestQuestions' => $this->quizStats->getEasiestQuestions(10, $period),
                'scoreDistribution' => $this->quizStats->getScoreDistribution($period),
                'trendsOverTime' => $this->quizStats->getTrendsOverTime($period),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch quiz statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get formation statistics
     */
    public function formationStats(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30d');
            
            $data = [
                'topFormations' => $this->formationStats->getTopFormations(10, $period),
                'completionRates' => $this->formationStats->getCompletionRatesByFormation($period),
                'enrollmentTrends' => $this->formationStats->getEnrollmentTrends($period),
                'averageProgress' => $this->formationStats->getAverageProgress($period),
                'dropoutRates' => $this->formationStats->getDropoutRates($period),
                'timeToComplete' => $this->formationStats->getAverageTimeToComplete($period),
                'studentsByFormation' => $this->formationStats->getStudentCountsByFormation(),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch formation statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get online users statistics
     */
    public function onlineUsers(Request $request): JsonResponse
    {
        try {
            $data = [
                'current' => $this->userActivity->getOnlineUsers(),
                'count' => $this->userActivity->getOnlineUsersCount(),
                'averageSessionTime' => $this->userActivity->getAverageSessionTime(),
                'peakHours' => $this->userActivity->getPeakHours(),
                'byRole' => $this->userActivity->getOnlineUsersByRole(),
                'byFormation' => $this->userActivity->getOnlineUsersByFormation(),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch online users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get affluence analytics
     */
    public function affluence(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '7d');
            
            $data = [
                'hourly' => $this->userActivity->getHourlyAffluence($period),
                'daily' => $this->userActivity->getDailyAffluence($period),
                'weekly' => $this->userActivity->getWeeklyAffluence(),
                'monthly' => $this->userActivity->getMonthlyAffluence(),
                'peakTimes' => $this->userActivity->getPeakTimes($period),
                'comparison' => $this->userActivity->getAffluenceComparison($period),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch affluence data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export statistics to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $type = $request->get('type', 'dashboard');
            $period = $request->get('period', '30d');
            
            // TODO: Implement PDF export with DomPDF or similar
            return response()->json([
                'message' => 'PDF export not yet implemented'
            ], 501);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to export PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export statistics to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $type = $request->get('type', 'dashboard');
            $period = $request->get('period', '30d');
            
            // TODO: Implement Excel export with Laravel Excel
            return response()->json([
                'message' => 'Excel export not yet implemented'
            ], 501);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to export Excel',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
