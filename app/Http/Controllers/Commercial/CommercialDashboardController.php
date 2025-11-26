<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial;
use App\Services\DashboardStatsService;
use Illuminate\Support\Facades\Auth;

class CommercialDashboardController extends Controller
{
    protected $statsService;

    public function __construct(DashboardStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index()
    {
        $commercial = Auth::user()->commercial;

        if (!$commercial) {
            abort(403, 'Unauthorized access');
        }

        // Stats générales
        $stats = [
            'total_stagiaires' => $commercial->stagiaires()->count(),
            'total_participations' => 0,
            'avg_score' => 0,
        ];

        // Récupérer les statistiques
        $statsByFormation = $this->statsService->getStatsByFormation(null, $commercial->id);
        $classement = $this->statsService->getClassement(null, $commercial->id, 20);
        $affluenceStats = $this->statsService->getAfflluenceStatsByCommercial($commercial->id, 'month');

        // Calculer les totaux
        $stats['total_participations'] = collect($statsByFormation)->sum('total_participations');
        $stats['avg_score'] = collect($statsByFormation)->avg('avg_score');

        // Récents quizzes
        $recentQuizzes = $this->statsService->getRecentQuizStats(10);
        $activeQuizzes = $this->statsService->getActiveQuizStats(10);

        return view('commercial.dashboard', compact(
            'commercial',
            'stats',
            'statsByFormation',
            'classement',
            'affluenceStats',
            'recentQuizzes',
            'activeQuizzes'
        ));
    }

    public function statsParFormation()
    {
        $commercial = Auth::user()->commercial;

        if (!$commercial) {
            abort(403, 'Unauthorized access');
        }

        $statsByFormation = $this->statsService->getStatsByFormation(null, $commercial->id);

        return view('commercial.stats.par-formation', compact('commercial', 'statsByFormation'));
    }

    public function statsParFormateur()
    {
        $commercial = Auth::user()->commercial;

        if (!$commercial) {
            abort(403, 'Unauthorized access');
        }

        $stagiairesIds = $commercial->stagiaires()->pluck('stagiaires.id');
        $formateurs = \App\Models\Formateur::whereHas('stagiaires', function ($q) use ($stagiairesIds) {
            $q->whereIn('stagiaires.id', $stagiairesIds);
        })->with('user')->get();

        $statsParFormateur = $formateurs->map(function ($formateur) use ($stagiairesIds) {
            $participations = \App\Models\QuizParticipation::whereIn(
                'user_id',
                $formateur->stagiaires()->whereIn('stagiaires.id', $stagiairesIds)->pluck('user_id')
            )
                ->where('status', 'completed')
                ->count();

            $avgScore = \App\Models\QuizParticipation::whereIn(
                'user_id',
                $formateur->stagiaires()->whereIn('stagiaires.id', $stagiairesIds)->pluck('user_id')
            )
                ->where('status', 'completed')
                ->avg('score') ?? 0;

            return [
                'id' => $formateur->id,
                'name' => $formateur->user->name,
                'stagiaires_count' => $formateur->stagiaires()->whereIn('stagiaires.id', $stagiairesIds)->count(),
                'total_participations' => $participations,
                'avg_score' => round($avgScore, 2),
            ];
        });

        return view('commercial.stats.par-formateur', compact('commercial', 'statsParFormateur'));
    }

    public function classement()
    {
        $commercial = Auth::user()->commercial;

        if (!$commercial) {
            abort(403, 'Unauthorized access');
        }

        $classement = $this->statsService->getClassement(null, $commercial->id, 100);

        return view('commercial.stats.classement', compact('commercial', 'classement'));
    }

    public function affluence()
    {
        $commercial = Auth::user()->commercial;

        if (!$commercial) {
            abort(403, 'Unauthorized access');
        }

        $affluenceData = $this->statsService->getAfflluenceStatsByCommercial($commercial->id, 'month');

        return view('commercial.stats.affluence', compact('commercial', 'affluenceData'));
    }
}
