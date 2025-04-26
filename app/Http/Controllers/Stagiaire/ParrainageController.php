<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\ParrainageService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            if ($user->role != 'formateur' && $user->role != 'admin') {
                // Vérifier si l'utilisateur est associé à ce stagiaire
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }

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
            return response()->json(['error' => 'Erreur lors de la génération du lien', 'message' => $e->getMessage()], 500);
        }
    }

    public function getParrainageRewards()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            if ($user->role != 'formateur' && $user->role != 'admin') {
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }

            $rewards = $this->parrainageService->getParrainageRewards($user->stagiaire->id);

            return response()->json($rewards);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des récompenses', 'message' => $e->getMessage()], 500);
        }
    }

    public function getParrainageHistory()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Convertir stagiaire en objet si c'est un tableau
            $userStagiaire = is_array($user->stagiaire) ? (object) $user->stagiaire : $user->stagiaire;

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            if ($user->role != 'formateur' && $user->role != 'admin') {
                if (!$userStagiaire || empty($userStagiaire->id)) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }

            $history = $this->parrainageService->getParrainageHistory($userStagiaire->id);


            // Format the history data
            $formattedHistory = array_map(function ($record) {
                return [
                    'id' => $record['id'] ?? null,
                    'parrain_id' => $record['parrain_id'] ?? null,
                    'filleul_id' => $record['filleul_id'] ?? null,
                    'token' => $record['token'] ?? null,
                    'nombre_filleul' => $record['nombre_filleul'] ?? null,
                    'lien' => $record['lien'] ?? null,
                    'points' => $record['points'] ?? null,
                    'created_at' => $record['created_at'] ?? null,
                    'updated_at' => $record['updated_at'] ?? null,
                    'filleul' => $record['filleul'] ?? null,
                ];
            }, $history);

            return response()->json($formattedHistory);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération de l\'historique', 'message' => $e->getMessage()], 500);
        }
    }

    public function acceptParrainage(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            if ($user->role != 'stagiaire') {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $validatedData = $request->validate([
                'token' => 'required|string',
            ]);

            $result = $this->parrainageService->acceptParrainage($user->stagiaire->id, $validatedData['token']);

            return response()->json($result);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Données invalides', 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'acceptation du parrainage', 'message' => $e->getMessage()], 500);
        }
    }
}
