<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\RankingService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    protected $rankingService;

    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function getGlobalRanking()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $ranking = $this->rankingService->getGlobalRanking();
            return response()->json($ranking);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getFormationRanking($formationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $ranking = $this->rankingService->getFormationRanking($formationId);
            return response()->json($ranking);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getMyRewards()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rewards = $this->rankingService->getStagiaireRewards($user->id);
            return response()->json($rewards);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getMyProgress()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $progress = $this->rankingService->getStagiaireProgress($user->id);
            return response()->json($progress);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
} 