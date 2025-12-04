<?php

namespace App\Services\Statistics;

use App\Models\Formation;
use App\Models\Stagiaire;
use App\Models\ProgressionStagiaire;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FormationStatisticsService
{
    /**
     * Get total number of formations
     */
    public function getTotalFormations(): int
    {
        return Formation::where('statut', 'actif')->count();
    }

    /**
     * Get average completion rate across all formations
     */
    public function getAverageCompletionRate(): float
    {
        $avg = ProgressionStagiaire::where('progression', 100)->count();
        $total = ProgressionStagiaire::count();
        
        return $total > 0 ? round(($avg / $total) * 100, 2) : 0;
    }

    /**
     * Get top formations by enrollment
     */
    public function getTopFormations(int $limit = 10, string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        
        return Formation::withCount(['stagiaires' => function ($query) use ($date) {
                $query->when($date, fn($q) => $q->where('created_at', '>=', $date));
            }])
            ->orderBy('stagiaires_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($formation) {
                return [
                    'id' => $formation->id,
                    'name' => $formation->titre,
                    'students_count' => $formation->stagiaires_count,
                    'completion_rate' => $this->getFormationCompletionRate($formation->id),
                ];
            })
            ->toArray();
    }

    /**
     * Get completion rates by formation
     */
    public function getCompletionRatesByFormation(string $period = '30d'): array
    {
        return Formation::with(['progressions' => function ($query) use ($period) {
                $date = $this->getPeriodDate($period);
                $query->when($date, fn($q) => $q->where('created_at', '>=', $date));
            }])
            ->get()
            ->map(function ($formation) {
                $total = $formation->progressions->count();
                $completed = $formation->progressions->where('progression', 100)->count();
                
                return [
                    'formation_id' => $formation->id,
                    'formation_name' => $formation->titre,
                    'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                    'total_enrollments' => $total,
                ];
            })
            ->toArray();
    }

    /**
     * Get enrollment trends over time
     */
    public function getEnrollmentTrends(string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        $groupBy = $this->getGroupByFormat($period);
        
        return DB::table('formation_stagiaire')
            ->when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->select(
                DB::raw($groupBy . ' as date'),
                DB::raw('COUNT(*) as enrollments')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Get average progress across formations
     */
    public function getAverageProgress(string $period = '30d'): float
    {
        $date = $this->getPeriodDate($period);
        
        return ProgressionStagiaire::when($date, fn($q) => $q->where('created_at', '>=', $date))
            ->avg('progression') ?? 0;
    }

    /**
     * Get dropout rates by formation
     */
    public function getDropoutRates(string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        
        return Formation::with(['progressions' => function ($query) use ($date) {
                $query->when($date, fn($q) => $q->where('updated_at', '<', Carbon::now()->subDays(30)))
                    ->where('progression', '<', 100);
            }])
            ->get()
            ->map(function ($formation) {
                $total = $formation->stagiaires()->count();
                $inactive = $formation->progressions
                    ->where('updated_at', '<', Carbon::now()->subDays(30))
                    ->where('progression', '<', 100)
                    ->count();
                
                return [
                    'formation_id' => $formation->id,
                    'formation_name' => $formation->titre,
                    'dropout_rate' => $total > 0 ? round(($inactive / $total) * 100, 2) : 0,
                    'inactive_count' => $inactive,
                ];
            })
            ->toArray();
    }

    /**
     * Get average time to complete formations
     */
    public function getAverageTimeToComplete(string $period = '30d'): array
    {
        $date = $this->getPeriodDate($period);
        
        return DB::table('progression_stagiaire')
            ->join('formations', 'progression_stagiaire.formation_id', '=', 'formations.id')
            ->where('progression_stagiaire.progression', 100)
            ->when($date, fn($q) => $q->where('progression_stagiaire.created_at', '>=', $date))
            ->select(
                'formations.id',
                'formations.titre as formation_name',
                DB::raw('AVG(DATEDIFF(progression_stagiaire.updated_at, progression_stagiaire.created_at)) as avg_days')
            )
            ->groupBy('formations.id', 'formations.titre')
            ->get()
            ->toArray();
    }

    /**
     * Get student counts by formation
     */
    public function getStudentCountsByFormation(): array
    {
        return Formation::withCount('stagiaires')
            ->orderBy('stagiaires_count', 'desc')
            ->get()
            ->map(function ($formation) {
                return [
                    'id' => $formation->id,
                    'name' => $formation->titre,
                    'count' => $formation->stagiaires_count,
                ];
            })
            ->toArray();
    }

    /**
     * Helper: Get completion rate for a specific formation
     */
    private function getFormationCompletionRate(int $formationId): float
    {
        $total = ProgressionStagiaire::where('formation_id', $formationId)->count();
        $completed = ProgressionStagiaire::where('formation_id', $formationId)
            ->where('progression', 100)
            ->count();
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
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
