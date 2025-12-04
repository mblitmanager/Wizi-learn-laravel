<?php

namespace App\Services\Statistics;

use App\Models\User;
use App\Models\Stagiaire;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UserActivityService
{
    /**
     * Get total number of students
     */
    public function getTotalStudents(): int
    {
        return Stagiaire::count();
    }

    /**
     * Get active students (logged in within last 30 days)
     */
    public function getActiveStudents(): int
    {
        return Stagiaire::whereHas('user', function ($query) {
            $query->where('last_activity', '>=', Carbon::now()->subDays(30));
        })->count();
    }

    /**
     * Get currently online users
     */
    public function getOnlineUsers(): array
    {
        return Cache::get('online_users', []);
    }

    /**
     * Get count of online users
     */
    public function getOnlineUsersCount(): int
    {
        return count($this->getOnlineUsers());
    }

    /**
     * Get recent activity (last X actions)
     */
    public function getRecentActivity(int $limit = 10): array
    {
        // TODO: Implement activity logging table
        return [];
    }

    /**
     * Get average session time
     */
    public function getAverageSessionTime(): string
    {
        // Calculate average from online users data
        $users = $this->getOnlineUsers();
        
        if (empty($users)) {
            return '00:00';
        }

        $totalSeconds = array_reduce($users, function ($carry, $user) {
            $connectedAt = Carbon::parse($user['connected_at'] ?? now());
            return $carry + now()->diffInSeconds($connectedAt);
        }, 0);

        $avgSeconds = count($users) > 0 ? $totalSeconds / count($users) : 0;
        $hours = floor($avgSeconds / 3600);
        $minutes = floor(($avgSeconds % 3600) / 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Get peak hours of activity
     */
    public function getPeakHours(): array
    {
        return DB::table('user_activity_log')
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as activity_count')
            )
            ->groupBy('hour')
            ->orderBy('activity_count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Get online users grouped by role
     */
    public function getOnlineUsersByRole(): array
    {
        $users = $this->getOnlineUsers();
        
        $grouped = [];
        foreach ($users as $user) {
            $role = $user['role'] ?? 'unknown';
            if (!isset($grouped[$role])) {
                $grouped[$role] = 0;
            }
            $grouped[$role]++;
        }

        return $grouped;
    }

    /**
     * Get online users grouped by formation
     */
    public function getOnlineUsersByFormation(): array
    {
        $users = $this->getOnlineUsers();
        
        $grouped = [];
        foreach ($users as $user) {
            $formation = $user['current_formation'] ?? 'none';
            if (!isset($grouped[$formation])) {
                $grouped[$formation] = 0;
            }
            $grouped[$formation]++;
        }

        return $grouped;
    }

    /**
     * Get hourly affluence
     */
    public function getHourlyAffluence(string $period = '7d'): array
    {
        $date = $this->getPeriodDate($period);
        
        return DB::table('user_activity_log')
            ->when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => sprintf('%02d:00', $item->hour),
                    'value' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get daily affluence
     */
    public function getDailyAffluence(string $period = '7d'): array
    {
        $date = $this->getPeriodDate($period);
        
        return DB::table('user_activity_log')
            ->when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => Carbon::parse($item->date)->format('d/m'),
                    'value' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get weekly affluence
     */
    public function getWeeklyAffluence(): array
    {
        return DB::table('user_activity_log')
            ->where('created_at', '>=', Carbon::now()->subDays(90))
            ->select(
                DB::raw('YEARWEEK(created_at) as week'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => 'Semaine ' . substr($item->week, -2),
                    'value' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get monthly affluence
     */
    public function getMonthlyAffluence(): array
    {
        return DB::table('user_activity_log')
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                    'value' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get peak times for a given period
     */
    public function getPeakTimes(string $period = '7d'): array
    {
        $date = $this->getPeriodDate($period);
        
        $hourly = DB::table('user_activity_log')
            ->when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('DAYNAME(created_at) as day'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour', 'day')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return $hourly->map(function ($item) {
            return [
                'time' => sprintf('%02d:00', $item->hour),
                'day' => $item->day,
                'count' => $item->count,
            ];
        })->toArray();
    }

    /**
     * Get affluence comparison (current vs previous period)
     */
    public function getAffluenceComparison(string $period = '7d'): array
    {
        $current = $this->getPeriodDate($period);
        $daysCount = $this->getPeriodDays($period);
        $previous = $this->getPeriodDate($period)->subDays($daysCount);

        $currentCount = DB::table('user_activity_log')
            ->where('created_at', '>=', $current)
            ->count();

        $previousCount = DB::table('user_activity_log')
            ->whereBetween('created_at', [$previous, $current])
            ->count();

        $change = $previousCount > 0 
            ? round((($currentCount - $previousCount) / $previousCount) * 100, 2)
            : 0;

        return [
            'current' => $currentCount,
            'previous' => $previousCount,
            'change_percent' => $change,
            'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable'),
        ];
    }

    /**
     * Helper: Get date from period string
     */
    private function getPeriodDate(string $period): Carbon
    {
        return match ($period) {
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            '90d' => Carbon::now()->subDays(90),
            '1y' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(30),
        };
    }

    /**
     * Helper: Get number of days in period
     */
    private function getPeriodDays(string $period): int
    {
        return match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30,
        };
    }
}
