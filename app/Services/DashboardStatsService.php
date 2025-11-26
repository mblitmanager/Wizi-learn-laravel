<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\Formateur;
use App\Models\Commercial;
use App\Models\Stagiaire;
use App\Models\Quiz;
use App\Models\QuizParticipation;
use App\Models\CatalogueFormation;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    private const DATE_FORMAT_DAY = '%Y-%m-%d';
    private const DATE_FORMAT_WEEK = '%Y-W%w';
    private const DATE_FORMAT_MONTH = '%Y-%m';
    private const DATE_FORMAT_YEAR = '%Y';

    /**
     * Obtenir les statistiques par formation
     */
    public function getStatsByFormation($formateurId = null, $commercialId = null)
    {
        $query = Formation::with([
            'stagiaires',
            'quizzes',
            'catalogueFormation'
        ]);

        if ($formateurId) {
            $query->whereHas('stagiaires', function ($q) use ($formateurId) {
                $q->whereHas('formateurs', function ($subQ) use ($formateurId) {
                    $subQ->where('formateur_id', $formateurId);
                });
            });
        }

        if ($commercialId) {
            $query->whereHas('stagiaires', function ($q) use ($commercialId) {
                $q->whereHas('commerciaux', function ($subQ) use ($commercialId) {
                    $subQ->where('commercial_id', $commercialId);
                });
            });
        }

        return $query->get()->map(function ($formation) {
            $participations = QuizParticipation::whereIn(
                'quiz_id',
                $formation->quizzes->pluck('id')
            )
                ->where('status', 'completed')
                ->count();

            $avgScore = QuizParticipation::whereIn(
                'quiz_id',
                $formation->quizzes->pluck('id')
            )
                ->where('status', 'completed')
                ->avg('score') ?? 0;

            return [
                'id' => $formation->id,
                'name' => $formation->nom,
                'stagiaires_count' => $formation->stagiaires()->count(),
                'quizzes_count' => $formation->quizzes()->count(),
                'total_participations' => $participations,
                'avg_score' => round($avgScore, 2),
                'catalogue' => $formation->catalogueFormation?->nom ?? 'N/A',
            ];
        });
    }

    /**
     * Obtenir les statistiques par formateur
     */
    public function getStatsByFormateur()
    {
        return Formateur::with(['user', 'stagiaires', 'catalogue_formations'])
            ->get()
            ->map(function ($formateur) {
                $stagiairesIds = $formateur->stagiaires()->pluck('stagiaires.id');

                $participations = QuizParticipation::whereIn(
                    'user_id',
                    Stagiaire::whereIn('id', $stagiairesIds)->pluck('user_id')
                )
                    ->where('status', 'completed')
                    ->count();

                $avgScore = QuizParticipation::whereIn(
                    'user_id',
                    Stagiaire::whereIn('id', $stagiairesIds)->pluck('user_id')
                )
                    ->where('status', 'completed')
                    ->avg('score') ?? 0;

                return [
                    'id' => $formateur->id,
                    'name' => $formateur->user->name,
                    'stagiaires_count' => $formateur->stagiaires()->count(),
                    'formations_count' => $formateur->catalogue_formations()->count(),
                    'total_participations' => $participations,
                    'avg_score' => round($avgScore, 2),
                ];
            });
    }

    /**
     * Obtenir les statistiques par catalogue de formation
     */
    public function getStatsByCatalogue()
    {
        return CatalogueFormation::with(['formations', 'formateurs'])
            ->get()
            ->map(function ($catalogue) {
                $formations = $catalogue->formations()->pluck('id');
                $quizzes = Quiz::whereIn('formation_id', $formations)->pluck('id');

                $stagiaires = Stagiaire::whereHas('formations', function ($q) use ($formations) {
                    $q->whereIn('formations.id', $formations);
                })->distinct()->count();

                $participations = QuizParticipation::whereIn('quiz_id', $quizzes)
                    ->where('status', 'completed')
                    ->count();

                $avgScore = QuizParticipation::whereIn('quiz_id', $quizzes)
                    ->where('status', 'completed')
                    ->avg('score') ?? 0;

                return [
                    'id' => $catalogue->id,
                    'name' => $catalogue->nom,
                    'formations_count' => $formations->count(),
                    'formateurs_count' => $catalogue->formateurs()->distinct()->count(),
                    'stagiaires_count' => $stagiaires,
                    'total_participations' => $participations,
                    'avg_score' => round($avgScore, 2),
                ];
            });
    }

    /**
     * Obtenir le classement des stagiaires
     */
    public function getClassement($formateurId = null, $commercialId = null, $limit = 50)
    {
        $query = Stagiaire::with(['user', 'commerciaux', 'formateurs'])
            ->select('stagiaires.*')
            ->addSelect(DB::raw(
                '(SELECT COALESCE(SUM(score), 0) FROM quiz_participations ' .
                'WHERE user_id = stagiaires.user_id AND status = "completed") as total_score'
            ))
            ->addSelect(DB::raw(
                '(SELECT COUNT(*) FROM quiz_participations ' .
                'WHERE user_id = stagiaires.user_id AND status = "completed") as total_quizzes'
            ));

        if ($formateurId) {
            $query->whereHas('formateurs', function ($q) use ($formateurId) {
                $q->where('formateur_id', $formateurId);
            });
        }

        if ($commercialId) {
            $query->whereHas('commerciaux', function ($q) use ($commercialId) {
                $q->where('commercial_id', $commercialId);
            });
        }

        return $query->orderByDesc('total_score')
            ->orderByDesc('total_quizzes')
            ->limit($limit)
            ->get()
            ->map(function ($stagiaire, $index) {
                $avgScore = $stagiaire->total_quizzes > 0
                    ? round($stagiaire->total_score / $stagiaire->total_quizzes, 2)
                    : 0;

                return [
                    'rank' => $index + 1,
                    'id' => $stagiaire->id,
                    'name' => $stagiaire->user->name,
                    'total_score' => $stagiaire->total_score,
                    'total_quizzes' => $stagiaire->total_quizzes,
                    'avg_score' => $avgScore,
                ];
            });
    }

    /**
     * Obtenir les statistiques d'affluence
     */
    public function getAfflluenceStats($period = 'month')
    {
        $dateFormat = $this->getDateFormat($period);

        return DB::table('quiz_participations')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as total_participations'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                DB::raw('AVG(score) as avg_score'),
                DB::raw('MAX(score) as max_score'),
                DB::raw('MIN(score) as min_score')
            )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(30)
            ->get();
    }

    /**
     * Obtenir les statistiques d'affluence par formateur
     */
    public function getAfflluenceStatsByFormateur($formateurId, $period = 'month')
    {
        $dateFormat = $this->getDateFormat($period);

        $stagiairesIds = Formateur::find($formateurId)
            ->stagiaires()
            ->pluck('stagiaires.id');

        return DB::table('quiz_participations')
            ->join('stagiaires', 'quiz_participations.user_id', '=', 'stagiaires.user_id')
            ->select(
                DB::raw("DATE_FORMAT(quiz_participations.created_at, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as total_participations'),
                DB::raw('COUNT(DISTINCT quiz_participations.user_id) as unique_users'),
                DB::raw('AVG(quiz_participations.score) as avg_score')
            )
            ->whereIn('stagiaires.id', $stagiairesIds)
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(30)
            ->get();
    }

    /**
     * Obtenir les statistiques d'affluence par commercial
     */
    public function getAfflluenceStatsByCommercial($commercialId, $period = 'month')
    {
        $dateFormat = $this->getDateFormat($period);

        $stagiairesIds = Commercial::find($commercialId)
            ->stagiaires()
            ->pluck('stagiaires.id');

        return DB::table('quiz_participations')
            ->join('stagiaires', 'quiz_participations.user_id', '=', 'stagiaires.user_id')
            ->select(
                DB::raw("DATE_FORMAT(quiz_participations.created_at, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as total_participations'),
                DB::raw('COUNT(DISTINCT quiz_participations.user_id) as unique_users'),
                DB::raw('AVG(quiz_participations.score) as avg_score')
            )
            ->whereIn('stagiaires.id', $stagiairesIds)
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(30)
            ->get();
    }

    /**
     * Obtenir le format de date approprié
     */
    private function getDateFormat($period): string
    {
        return match($period) {
            'day' => self::DATE_FORMAT_DAY,
            'week' => self::DATE_FORMAT_WEEK,
            'year' => self::DATE_FORMAT_YEAR,
            default => self::DATE_FORMAT_MONTH,
        };
    }

    /**
     * Obtenir les quiz récents avec statistiques
     */
    public function getRecentQuizStats($limit = 10)
    {
        return Quiz::with('formation')
            ->select('quizzes.*')
            ->selectRaw('(SELECT COUNT(*) FROM quiz_participations WHERE quiz_id = quizzes.id) as total_attempts')
            ->selectRaw('(SELECT AVG(score) FROM quiz_participations WHERE quiz_id = quizzes.id) as avg_score')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir les quiz actifs avec statistiques
     */
    public function getActiveQuizStats($limit = 10)
    {
        return Quiz::with('formation')
            ->select('quizzes.*')
            ->selectRaw('(SELECT COUNT(*) FROM quiz_participations WHERE quiz_id = quizzes.id AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as recent_attempts')
            ->selectRaw('(SELECT AVG(score) FROM quiz_participations WHERE quiz_id = quizzes.id AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as recent_avg_score')
            ->where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

