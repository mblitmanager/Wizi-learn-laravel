<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function getTutorials()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $tutorials = $this->mediaService->getTutorials();
            return response()->json($tutorials);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getLanguageSessions()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $sessions = $this->mediaService->getLanguageSessions();
            return response()->json($sessions);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getInteractiveFormations()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $formations = $this->mediaService->getInteractiveFormations();
            return response()->json($formations);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
} 