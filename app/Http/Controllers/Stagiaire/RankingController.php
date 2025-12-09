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

            // Récupérer le classement global
            $classements = Classement::with(['stagiaire.user', 'quiz'])
                ->get()
                ->groupBy('stagiaire_id')
                ->map(function ($group) {
                    return [
                        'stagiaire' => [
                            'id' => (string)$group->first()->stagiaire->id,
                            'prenom' => $group->first()->stagiaire->prenom,
                            'image' => $group->first()->stagiaire->user->image ?? null
                        ],
                        'totalPoints' => $group->sum('points'),
                        'quizCount' => $group->count(),
                        'averageScore' => $group->avg('points')
                    ];
                })
                ->sortByDesc('totalPoints')
                ->values();

            // Trouver le classement de l'utilisateur connecté
            $myClassement = $classements->first(function ($item) use ($user) {
                return $item['stagiaire']['id'] == (string)$user->stagiaire->id;
            });

            $progress = $this->rankingService->getStagiaireProgress($user->stagiaire->id);
            $user->stagiaire->load(['user', 'catalogue_formations', 'formateur', 'commercial']);
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

    /**
     * Get detailed information about a specific stagiaire
     * Including formations, formateurs, and quiz statistics
     */
    public function getStagiaireDetails($stagiaireId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            // Load stagiaire with all necessary relations
            $stagiaire = \App\Models\Stagiaire::with([
                'user',
                'formateurs',
                'catalogue_formations',
                'classements.quiz'
            ])->findOrFail($stagiaireId);

            // Calculate total points
            $totalPoints = $stagiaire->classements->sum('points');
            
            // Calculate quiz stats by level
            $quizByLevel = $stagiaire->classements->groupBy(function($classement) {
                return $classement->quiz->niveau ?? 'inconnu';
            })->map(function($group) {
                return [
                    'completed' => $group->count(),
                    'total' => $group->count(), // You might want to calculate actual total available
                ];
            });

            // Get total quizzes
            $totalQuizzes = $stagiaire->classements->count();
            
            // Calculate success percentage (assuming points above 70% is success)
            $successCount = $stagiaire->classements->filter(function($c) {
                $quiz = $c->quiz;
                if (!$quiz || !$quiz->nb_points_total) return false;
                return ($c->points / $quiz->nb_points_total) >= 0.7;
            })->count();
            
            $successPercentage = $totalQuizzes > 0 ? round(($successCount / $totalQuizzes) * 100, 2) : 0;

            // Get last activity
            $lastActivity = $stagiaire->classements->max('updated_at');

            // Calculate rang in global ranking
            $allClassements = \App\Models\Classement::select('stagiaire_id')
                ->selectRaw('SUM(points) as total_points')
                ->groupBy('stagiaire_id')
                ->orderByDesc('total_points')
                ->get();
            
            $rang = $allClassements->search(function($item) use ($stagiaireId) {
                return $item->stagiaire_id == $stagiaireId;
            }) + 1;

            return response()->json([
                'id' => $stagiaire->id,
                'firstname' => $stagiaire->prenom,
                'name' => $stagiaire->user->name ?? '',
                'avatar' => $stagiaire->user->image ?? null,
                'rang' => $rang ?: 999,
                'totalPoints' => $totalPoints,
                'formations' => $stagiaire->catalogue_formations->map(function($formation) {
                    return [
                        'id' => $formation->id,
                        'titre' => $formation->titre ?? 'Formation sans titre',
                    ];
                }),
                'formateurs' => $stagiaire->formateurs->map(function($formateur) {
                    return [
                        'id' => $formateur->id,
                        'prenom' => $formateur->prenom,
                        'nom' => $formateur->user->nom,
                        'image' => $formateur->user->image ?? null,
                    ];
                }),
                'quizStats' => [
                    'totalCompleted' => $totalQuizzes,
                    'totalQuiz' => $totalQuizzes,
                    'pourcentageReussite' => $successPercentage,
                    'byLevel' => [
                        'debutant' => $quizByLevel->get('debutant', ['completed' => 0, 'total' => 0]),
                        'intermediaire' => $quizByLevel->get('intermediaire', ['completed' => 0, 'total' => 0]),
                        'expert' => $quizByLevel->get('expert', ['completed' => 0, 'total' => 0]),
                    ],
                    'lastActivity' => $lastActivity ? $lastActivity->toIso8601String() : null,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getStagiaireDetails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des détails du stagiaire',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user's total points and accessible quiz levels
     */
    public function getUserPoints()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            $stagiaire = $user->stagiaire;
            if (!$stagiaire) {
                return response()->json(['error' => 'Stagiaire not found'], 404);
            }

            // Calculate total points
            $totalPoints = $stagiaire->classements->sum('points');

            // Determine accessible levels based on points
            $accessibleLevels = [];
            if ($totalPoints < 50) {
                $accessibleLevels = ['debutant'];
            } else if ($totalPoints < 100) {
                $accessibleLevels = ['debutant', 'intermediaire'];
            } else {
                $accessibleLevels = ['debutant', 'intermediaire', 'expert'];
            }

            return response()->json([
                'totalPoints' => $totalPoints,
                'accessibleLevels' => $accessibleLevels,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getUserPoints', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Unable to retrieve user points',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
