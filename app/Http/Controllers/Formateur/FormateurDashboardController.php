<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\CatalogueFormation;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FormateurDashboardController extends Controller
{
    protected $statsService;

    public function __construct(DashboardStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index()
    {
        $formateur = Auth::user()->formateur;

        if (!$formateur) {
            abort(403, 'Unauthorized access');
        }

        // Statistiques pour le formateur
        $stats = [
            'total_stagiaires' => $formateur->stagiaires()->count(),
            'stagiaires_en_cours' => $formateur->stagiaires()
                ->where('statut', 1)
                ->where(function ($query) {
                    $query->whereNull('date_fin_formation')
                        ->orWhere('date_fin_formation', '>', now());
                })
                ->count(),
            'stagiaires_termines' => $formateur->stagiaires()
                ->where('statut', 0)
                ->where('date_fin_formation', '>=', Carbon::now()->subYear())
                ->count(),
            'formations_encadrees' => $formateur->catalogue_formations()->count()
        ];

        // Statistiques par formation
        $statsByFormation = $this->statsService->getStatsByFormation($formateur->id);

        // Classement des stagiaires
        $classement = $this->statsService->getClassement($formateur->id, limit: 20);

        // Affluence
        $affluenceStats = $this->statsService->getAfflluenceStatsByFormateur($formateur->id, 'month');

        // Derniers stagiaires ajoutés
        $recentStagiaires = $formateur->stagiaires()
            ->with(['user', 'catalogue_formations'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Récents et actifs quizzes
        $recentQuizzes = $this->statsService->getRecentQuizStats(10);
        $activeQuizzes = $this->statsService->getActiveQuizStats(10);

        return view('formateur.dashboard', compact(
            'formateur',
            'stats',
            'statsByFormation',
            'classement',
            'affluenceStats',
            'recentStagiaires',
            'recentQuizzes',
            'activeQuizzes'
        ));
    }

    /**
     * Statistiques détaillées par formation
     */
    public function statsParFormation()
    {
        $formateur = Auth::user()->formateur;

        if (!$formateur) {
            abort(403, 'Unauthorized access');
        }

        $statsByFormation = $this->statsService->getStatsByFormation($formateur->id);

        return view('formateur.stats.par-formation', compact('formateur', 'statsByFormation'));
    }

    /**
     * Classement détaillé
     */
    public function classement()
    {
        $formateur = Auth::user()->formateur;

        if (!$formateur) {
            abort(403, 'Unauthorized access');
        }

        $classement = $this->statsService->getClassement($formateur->id, limit: 100);

        return view('formateur.stats.classement', compact('formateur', 'classement'));
    }

    /**
     * Affluence détaillée
     */
    public function affluence()
    {
        $formateur = Auth::user()->formateur;

        if (!$formateur) {
            abort(403, 'Unauthorized access');
        }

        $affluenceData = $this->statsService->getAfflluenceStatsByFormateur($formateur->id, 'month');

        return view('formateur.stats.affluence', compact('formateur', 'affluenceData'));
    }
}
