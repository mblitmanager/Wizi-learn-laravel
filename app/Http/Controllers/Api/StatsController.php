<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Stagiaire;
use App\Models\Participation;
use App\Models\QuizParticipation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * Get statistics data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $range = $request->get('range', '7d');
        $metric = $request->get('metric', 'signups');

        // Determine date range
        $startDate = $this->getStartDate($range);
        $endDate = now();

        // Cache key
        $cacheKey = "stats_{$range}_{$metric}_" . $startDate->format('Y-m-d');

        // Cache for 5 minutes
        $data = Cache::remember($cacheKey, 300, function () use ($startDate, $endDate, $metric) {
            return [
                'summary' => $this->getSummaryStats($startDate, $endDate),
                'chartData' => $this->getChartData($startDate, $endDate, $metric),
            ];
        });

        return response()->json($data);
    }

    /**
     * Get summary statistics
     */
    private function getSummaryStats($startDate, $endDate)
    {
        // Inscriptions (new users)
        $signups = User::whereBetween('created_at', [$startDate, $endDate])->count();

        // Active sessions (users active in last 24 hours)
        $activeSessions = User::where('last_activity_at', '>=', now()->subDay())->count();

        // Revenue (if you have a payments table)
        // Assuming you might have this - adjust table name as needed
        $revenue = DB::table('payments')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount') ?? 0;

        // Completed courses
        $completedCourses = DB::table('progressions')
            ->where('progression', 100)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            'signups' => $signups,
            'activeSessions' => $activeSessions,
            'revenue' => round($revenue, 2),
            'completedCourses' => $completedCourses,
        ];
    }

    /**
     * Get chart data for selected metric
     */
    private function getChartData($startDate, $endDate, $metric)
    {
        $days = $startDate->diffInDays($endDate);
        $chartData = [];

        // Generate data points based on range
        $points = min($days, 30); // Max 30 data points
        $interval = max(1, floor($days / $points));

        for ($i = 0; $i <= $points; $i++) {
            $date = (clone $startDate)->addDays($i * $interval);
            $nextDate = (clone $date)->addDays($interval);

            $value = 0;

            switch ($metric) {
                case 'signups':
                    $value = User::whereBetween('created_at', [$date, $nextDate])->count();
                    break;
                case 'activeSessions':
                    $value = User::where('last_activity_at', '>=', $date)
                        ->where('last_activity_at', '<', $nextDate)
                        ->count();
                    break;
                case 'revenue':
                    $value = DB::table('payments')
                        ->whereBetween('created_at', [$date, $nextDate])
                        ->sum('amount') ?? 0;
                    break;
                case 'completedCourses':
                    $value = DB::table('progressions')
                        ->where('progression', 100)
                        ->whereBetween('created_at', [$date, $nextDate])
                        ->count();
                    break;
            }

            $chartData[] = [
                'date' => $date->format('d/m'),
                'value' => $value,
            ];
        }

        return $chartData;
    }

    /**
     * Get start date based on range
     */
    private function getStartDate($range)
    {
        switch ($range) {
            case '7d':
                return now()->subDays(7);
            case '30d':
                return now()->subDays(30);
            case '90d':
                return now()->subDays(90);
            case '1y':
                return now()->subYear();
            default:
                return now()->subDays(7);
        }
    }
}
