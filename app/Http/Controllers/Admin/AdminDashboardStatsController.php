<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formateur;
use App\Models\Commercial;
use App\Services\DashboardStatsService;

class AdminDashboardStatsController extends Controller
{
    protected $statsService;

    public function __construct(DashboardStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Dashboard global avec statistiques par formation
     */
    public function statsParFormation()
    {
        $statsByFormation = $this->statsService->getStatsByFormation();

        return view('admin.dashboard.stats-par-formation', compact('statsByFormation'));
    }

    /**
     * Statistiques par formateur
     */
    public function statsParFormateur()
    {
        $statsByFormateur = $this->statsService->getStatsByFormateur();

        return view('admin.dashboard.stats-par-formateur', compact('statsByFormateur'));
    }

    /**
     * Statistiques par catalogue
     */
    public function statsParCatalogue()
    {
        $statsByCatalogue = $this->statsService->getStatsByCatalogue();

        return view('admin.dashboard.stats-par-catalogue', compact('statsByCatalogue'));
    }

    /**
     * Classement global
     */
    public function classement()
    {
        $classement = $this->statsService->getClassement(limit: 100);

        return view('admin.dashboard.classement', compact('classement'));
    }

    /**
     * Statistiques d'affluence
     */
    public function affluence()
    {
        $affluenceData = $this->statsService->getAfflluenceStats('month');

        return view('admin.dashboard.affluence', compact('affluenceData'));
    }
}
