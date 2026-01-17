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
        $this->middleware('commercial');
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
            $query->where('last_activity_at', '>=', Carbon::now()->subDays(30));
        })->count();
    }

    private function getConversionRate(?string $period = null): float
    {
        // Conversion rate: Active students / Total students
        $total = $this->getTotalSignups($period);
        $date = $period ? $this->getPeriodDate($period) : Carbon::now()->subDays(30);
        
        $active = Stagiaire::whereHas('user', function($q) use ($date) {
            $q->where('last_activity_at', '>=', $date);
        })->count();
        
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
                    'role' => $stagiaire->user->role ?? 'stagiaire',
                    'created_at' => $stagiaire->created_at->format('Y-m-d H:i'),
                ];
            })
            ->toArray();
    }

    private function getTopSellingFormations(int $limit = 5, ?string $period = null): array
    {
        $query = \App\Models\CatalogueFormation::withCount(['stagiaires' => function ($q) use ($period) {
            if ($period) {
                $q->where('stagiaire_catalogue_formations.created_at', '>=', $this->getPeriodDate($period));
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
                    'price' => $formation->tarif ?? 0,
                ];
            })
            ->toArray();
    }

    private function getSignupTrends(string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        
        // MySQL specific DATE_FORMAT for trends
        $results = Stagiaire::where('created_at', '>=', $date)
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw('COUNT(*) as signups')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        return $results->map(function ($item) {
            return [
                'date' => $item->date,
                'value' => $item->signups,
            ];
        })->toArray();
    }

    private function estimateRevenue(?string $period = null): float
    {
        $date = $period ? $this->getPeriodDate($period) : Carbon::now()->subDays(30);
        
        return DB::table('stagiaire_catalogue_formations')
            ->join('catalogue_formations', 'stagiaire_catalogue_formations.catalogue_formation_id', '=', 'catalogue_formations.id')
            ->where('stagiaire_catalogue_formations.created_at', '>=', $date)
            ->sum('catalogue_formations.tarif') ?? 0;
    }

    private function getFormationBreakdown(?string $period = null): array
    {
        $date = $period ? $this->getPeriodDate($period) : Carbon::now()->subDays(30);
        
        return DB::table('stagiaire_catalogue_formations')
            ->join('catalogue_formations', 'stagiaire_catalogue_formations.catalogue_formation_id', '=', 'catalogue_formations.id')
            ->where('stagiaire_catalogue_formations.created_at', '>=', $date)
            ->select(
                'catalogue_formations.titre as formation_name',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('catalogue_formations.id', 'catalogue_formations.titre')
            ->get()
            ->toArray();
    }

    private function getConversionByFormation(?string $period = null): array
    {
        $date = $period ? $this->getPeriodDate($period) : Carbon::now()->subDays(30);
        
        return \App\Models\CatalogueFormation::withCount(['stagiaires as total'])
            ->withCount(['stagiaires as active' => function($q) use ($date) {
                $q->whereHas('user', function($u) use ($date) {
                    $u->where('last_activity_at', '>=', $date);
                });
            }])
            ->get()
            ->map(function($f) {
                return [
                    'name' => $f->titre,
                    'rate' => $f->total > 0 ? round(($f->active / $f->total) * 100, 2) : 0
                ];
            })
            ->toArray();
    }

    private function getFunnelAnalysis(?string $period = null): array
    {
        $date = $period ? $this->getPeriodDate($period) : Carbon::now()->subDays(30);
        
        return [
            'registered' => Stagiaire::where('created_at', '>=', $date)->count(),
            'enrolled' => DB::table('stagiaire_catalogue_formations')->where('created_at', '>=', $date)->distinct('stagiaire_id')->count(),
            'active' => Stagiaire::whereHas('user', function($q) use ($date) {
                $q->where('last_activity_at', '>=', $date);
            })->count(),
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
            '7d' => "%Y-%m-%d",
            '30d' => "%Y-%m-%d",
            '90d' => "%Y-%u",
            '1y' => "%Y-%m",
            default => "%Y-%m-%d",
        };
    }
}
