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
             // Charger la relation stagiaire si elle n'est pas déjà chargée
             if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            if ($user->role != 'formateur' && $user->role != 'admin') {
                // Vérifier si l'utilisateur est associé à ce stagiaire
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }
            $ranking = $this->rankingService->getGlobalRanking();
            return response()->json($ranking);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 403);
        }
    }

    public function getFormationRanking($formationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            $ranking = $this->rankingService->getFormationRanking($formationId);
            return response()->json($ranking);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 403);
        }
    }

    public function getMyRewards()
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
             // Charger la relation stagiaire si elle n'est pas déjà chargée
             if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            if ($user->role != 'formateur' && $user->role != 'admin') {
                // Vérifier si l'utilisateur est associé à ce stagiaire
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }
            $rewards = $this->rankingService->getStagiaireRewards($user->stagiaire->id);
            return response()->json($rewards);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 403);
        }
    }

    public function getMyProgress()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            if ($user->role != 'formateur' && $user->role != 'admin') {
                // Vérifier si l'utilisateur est associé à ce stagiaire
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }

            $progress = $this->rankingService->getStagiaireProgress($user->stagiaire->id);

            // Charger les relations nécessaires pour le stagiaire
            $user->stagiaire->load(['user', 'formations', 'formateur', 'commercial']);

            return response()->json([
                'stagiaire' => $user->stagiaire,
                'progress' => $progress
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 403);
        }
    }
}