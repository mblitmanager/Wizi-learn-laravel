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
            $link = $this->parrainageService->getParrainageLink($user->stagiaire->id);
            return response()->json(['link' => $link]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
    }

    public function getFilleuls()
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
            $filleuls = $this->parrainageService->getFilleuls($user->stagiaire->id);

            return response()->json($filleuls);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
    }

    public function getParrainageStats()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
             // Charger la relation stagiaire si elle n'est pas déjà chargée
             if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            $stats = $this->parrainageService->getParrainageStats($user->stagiaire->id);
            return response()->json($stats);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
    }

    /**
     * Génère un lien de parrainage pour le stagiaire connecté
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateParrainageLink()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien un stagiaire
            if ($user->role !== 'stagiaire') {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Générer le lien de parrainage
            $link = $this->parrainageService->generateParrainageLink($user->stagiaire->id);

            return response()->json([
                'success' => true,
                'link' => $link
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la génération du lien'], 500);
        }
    }
}