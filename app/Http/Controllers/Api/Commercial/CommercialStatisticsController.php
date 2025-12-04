<?php

namespace App\Http\Controllers\Api\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommercialStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // TODO: Add commercial role middleware
        // $this->middleware('role:commercial');
    }

    /**
     * Get commercial dashboard overview
     */
    public function dashboard(): JsonResponse
    {
        try {
            $data = [
                'summary' => [
                    'totalSignups' => $this->getTotalSignups(),
                    'signupsThisMonth' => $this->getSignupsThisMonth(),
                    'activeStudents' => $this->getActiveStudents(),
                    'conversionRate' => $this->getConversionRate(),
                ],
                'recentSignups' => $this->getRecentSignups(10),
                'topFormations' => $this->getTopSellingFormations(5),
                'signupTrends' => $this->getSignupTrends('30d'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch commercial dashboard',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales statistics
     */
    public function salesStats(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30d');
            
            $data = [
                'totalSignups' => $this->getTotalSignups($period),
                'revenue' => $this->estimateRevenue($period),
                'formationBreakdown' => $this->getFormationBreakdown($period),
                'trends' => $this->getSignupTrends($period),
                'topPerformers' => $this->getTopSellingFormations(10, $period),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch sales statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversion statistics
     */
    public function conversionStats(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30d');
            
            $data = [
                'overallConversion' => $this->getConversionRate($period),
                'byFormation' => $this->getConversionByFormation($period),
                'funnelAnalysis' => $this->getFunnelAnalysis($period),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch conversion statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper methods
     */
    
    private function getTotalSignups(?string $period = null): int
    {
        $query = Stagiaire::query();
        
        if ($period) {
            $date = $this->getPeriodDate($period);
            $query->where('created_at', '>=', $date);
        }
        
        return $query->count();
    }

    private function getSignupsThisMonth(): int
    {
        return Stagiaire::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
    }

    private function getActiveStudents(): int
    {
        return Stagiaire::whereHas('user', function ($query) {
            $query->where('last_activity', '>=', Carbon::now()->subDays(30));
        })->count();
    }

    private function getConversionRate(?string $period = null): float
    {
        // TODO: Implement actual conversion tracking
        // For now, return active/total ratio
        $total = $this->getTotalSignups($period);
        $active = $period 
            ? Stagiaire::whereHas('user', fn($q) => $q->where('last_activity', '>=', $this->getPeriodDate($period)))->count()
            : $this->getActiveStudents();
        
        return $total > 0 ? round(($active / $total) * 100, 2) : 0;
    }

    private function getRecentSignups(int $limit = 10): array
    {
        return Stagiaire::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($stagiaire) {
                return [
                    'id' => $stagiaire->id,
                    'name' => $stagiaire->user->name ?? 'Unknown',
                    'email' => $stagiaire->user->email ?? '',
                    'created_at' => $stagiaire->created_at->format('Y-m-d H:i'),
                ];
            })
            ->toArray();
    }

    private function getTopSellingFormations(int $limit = 5, ?string $period = null): array
    {
        $query = Formation::withCount(['stagiaires' => function ($q) use ($period) {
            if ($period) {
                $q->where('created_at', '>=', $this->getPeriodDate($period));
            }
        }]);
        
        return $query->orderBy('stagiaires_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($formation) {
                return [
                    'id' => $formation->id,
                    'name' => $formation->titre,
                    'enrollments' => $formation->stagiaires_count,
                    'revenue' => $formation->stagiaires_count * 100, // TODO: Use actual price
                ];
            })
            ->toArray();
    }

    private function getSignupTrends(string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        $groupBy = $this->getGroupByFormat($period);
        
        return Stagiaire::where('created_at', '>=', $date)
            ->select(
                DB::raw($groupBy . ' as date'),
                DB::raw('COUNT(*) as signups')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'value' => $item->signups,
                ];
            })
            ->toArray();
    }

    private function estimateRevenue(?string $period = null): float
    {
        // TODO: Implement with actual pricing data
        return $this->getTotalSignups($period) * 100; // Placeholder
    }

    private function getFormationBreakdown(?string $period = null): array
    {
        $query = DB::table('formation_stagiaire')
            ->join('formations', 'formation_stagiaire.formation_id', '=', 'formations.id')
            ->select(
                'formations.titre as formation_name',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('formations.id', 'formations.titre');
        
        if ($period) {
            $query->where('formation_stagiaire.created_at', '>=', $this->getPeriodDate($period));
        }
        
        return $query->get()->toArray();
    }

    private function getConversionByFormation(?string $period = null): array
    {
        // TODO: Implement with actual conversion tracking
        return [];
    }

    private function getFunnelAnalysis(?string $period = null): array
    {
        // TODO: Implement funnel tracking
        return [
            'visited' => 1000,
            'registered' => 500,
            'enrolled' => 300,
            'active' => 200,
        ];
    }

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
