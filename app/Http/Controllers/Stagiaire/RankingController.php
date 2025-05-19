<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\RankingService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Models\Classement;
use Illuminate\Support\Facades\Log;

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
            // Récupérer tous les classements avec leurs relations
            $classements = Classement::with(['stagiaire.user', 'quiz'])
                ->get()
                ->groupBy('stagiaire_id')
                ->map(function ($group) {
                    $totalPoints = $group->sum('points');
                    return [
                        'stagiaire' => [
                            'id' => (string)$group->first()->stagiaire->id,
                            'prenom' => $group->first()->stagiaire->prenom,
                            'image' => $group->first()->stagiaire->user->image ?? null
                        ],
                        'totalPoints' => $totalPoints,
                        'quizCount' => $group->count(),
                        'averageScore' => $group->avg('points'),
                        // Le level sera ajouté après le map
                    ];
                })
                ->sortByDesc('totalPoints')
                ->values();

            // Ajouter le rang et le level
            $classements = $classements->map(function ($item, $index) {
                $level = $this->rankingService->calculateLevel($item['totalPoints']);
                return [
                    ...$item,
                    'rang' => $index + 1,
                    'level' => $level
                ];
            });

            return response()->json($classements);
        } catch (\Exception $e) {
            Log::error('Erreur dans getGlobalClassement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération du classement global',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function getMyRanking()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            $stagiaire = $user->stagiaire;
            if (!$stagiaire) {
                return response()->json(['error' => 'non autorisé'], 403);
            }

            // Récupérer tous les classements avec leurs relations
            $classements = Classement::with(['stagiaire.user', 'quiz'])
                ->get()
                ->groupBy('stagiaire_id')
                ->map(function ($group) {
                    $totalPoints = $group->sum('points');
                    return [
                        'stagiaire' => [
                            'id' => (string)$group->first()->stagiaire->id,
                            'prenom' => $group->first()->stagiaire->prenom,
                            'image' => $group->first()->stagiaire->user->image ?? null
                        ],
                        'totalPoints' => $totalPoints,
                        'quizCount' => $group->count(),
                        'averageScore' => $group->avg('points'),
                    ];
                })
                ->sortByDesc('totalPoints')
                ->values();

            // Ajouter le rang et le level
            $classements = $classements->map(function ($item, $index) {
                $level = $this->rankingService->calculateLevel($item['totalPoints']);
                return [
                    ...$item,
                    'rang' => $index + 1,
                    'level' => $level
                ];
            });

            // Trouver le classement de l'utilisateur connecté
            $myClassement = $classements->first(function ($item) use ($stagiaire) {
                return $item['stagiaire']['id'] == (string)$stagiaire->id;
            });

            if (!$myClassement) {
                return response()->json(['error' => 'Aucun classement trouvé pour ce stagiaire'], 404);
            }

            return response()->json($myClassement);
        } catch (\Exception $e) {
            Log::error('Erreur dans getMyRanking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération de votre classement',
                'message' => $e->getMessage()
            ], 500);
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
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }
            if ($user->role != 'formateur' && $user->role != 'admin') {
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }
            $stagiaire = $user->stagiaire;
            // Calculer le classement du stagiaire (même logique que getMyRanking, mais ici en tableau)
            $classements = Classement::with(['stagiaire.user', 'quiz'])
                ->get()
                ->groupBy('stagiaire_id')
                ->map(function ($group) {
                    $totalPoints = $group->sum('points');
                    return [
                        'stagiaire' => [
                            'id' => (string)$group->first()->stagiaire->id,
                            'prenom' => $group->first()->stagiaire->prenom,
                            'image' => $group->first()->stagiaire->user->image ?? null
                        ],
                        'totalPoints' => $totalPoints,
                        'quizCount' => $group->count(),
                        'averageScore' => $group->avg('points'),
                    ];
                })
                ->sortByDesc('totalPoints')
                ->values();
            $classements = $classements->map(function ($item, $index) {
                $level = $this->rankingService->calculateLevel($item['totalPoints']);
                return [
                    ...$item,
                    'rang' => $index + 1,
                    'level' => $level
                ];
            });
            $myClassement = $classements->first(function ($item) use ($stagiaire) {
                return $item['stagiaire']['id'] == (string)$stagiaire->id;
            });
            $progress = $this->rankingService->getStagiaireProgress($user->stagiaire->id);
            $user->stagiaire->load(['user', 'formations', 'formateur', 'commercial']);
            $response = [
                'stagiaire' => [
                    'id' => (string)$user->stagiaire->id,
                    'prenom' => $user->stagiaire->prenom,
                    'image' => $user->stagiaire->user->image ?? null
                ],
                'totalPoints' => $myClassement['totalPoints'] ?? 0,
                'quizCount' => $progress['completed_quizzes'],
                'averageScore' => $myClassement['averageScore'] ?? 0,
                'completedQuizzes' => $myClassement['quizCount'] ?? 0,
                'totalTimeSpent' => $progress['total_time_spent'],
                'rang' => $progress['rang'],
                'level' => $myClassement['level'] ?? 0
            ];

            return response()->json($response);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 403);
        }
    }
}
