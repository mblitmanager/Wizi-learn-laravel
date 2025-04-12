<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\ParrainageService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class ParrainageController extends Controller
{
    protected $parrainageService;

    public function __construct(ParrainageService $parrainageService)
    {
        $this->parrainageService = $parrainageService;
    }

    public function getParrainageLink()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $link = $this->parrainageService->getParrainageLink($user->id);
            return response()->json(['link' => $link]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getFilleuls()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $filleuls = $this->parrainageService->getFilleuls($user->id);
            return response()->json($filleuls);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getParrainageStats()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stats = $this->parrainageService->getParrainageStats($user->id);
            return response()->json($stats);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
} 