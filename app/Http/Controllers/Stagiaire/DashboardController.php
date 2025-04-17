<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getDashboard()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $dashboard = $this->dashboardService->getStagiaireDashboard($user->id);
            return response()->json($dashboard);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getQuizStatistics()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stats = $this->dashboardService->getQuizStatistics($user->id);
            return response()->json($stats);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getFormationStatistics()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stats = $this->dashboardService->getFormationStatistics($user->id);
            return response()->json($stats);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getComparisonStatistics()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stats = $this->dashboardService->getComparisonStatistics($user->id);
            return response()->json($stats);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
} 