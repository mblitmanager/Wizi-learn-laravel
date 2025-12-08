<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Classement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormationClassementController extends Controller
{
    /**
     * Obtenir le classement d'une formation spécifique
     * Agrège les points de tous les quiz de cette formation
     *
     * @param int $formationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClassement($formationId)
    {
        try {
            $formation = Formation::with('quizzes')->findOrFail($formationId);
            $quizIds = $formation->quizzes->pluck('id')->toArray();

            if (empty($quizIds)) {
                return response()->json([
                    'formation' => [
                        'id' => $formation->id,
                        'titre' => $formation->titre,
                    ],
                    'classement' => [],
                ]);
            }

            // Agréger les points par stagiaire pour cette formation
            $classementData = Classement::whereIn('quiz_id', $quizIds)
                ->select([
                    'stagiaire_id',
                    DB::raw('SUM(points) as total_points'),
                    DB::raw('COUNT(DISTINCT quiz_id) as quiz_count'),
                    DB::raw('AVG(points) as avg_points'),
                ])
                ->groupBy('stagiaire_id')
                ->orderByDesc('total_points')
                ->get();

            // Charger les relations pour chaque stagiaire
            $classement = $classementData->map(function ($item, $index) {
                $stagiaire = \App\Models\Stagiaire::with('user')->find($item->stagiaire_id);
                
                if (!$stagiaire) {
                    return null;
                }

                return [
                    'rang' => $index + 1,
                    'stagiaire_id' => $stagiaire->id,
                    'user' => [
                        'id' => $stagiaire->user->id,
                        'nom' => $stagiaire->user->nom,
                        'prenom' => $stagiaire->user->prenom,
                        'email' => $stagiaire->user->email,
                        'avatar' => $stagiaire->user->avatar ?? null,
                    ],
                    'total_points' => (int) $item->total_points,
                    'quiz_completes' => (int) $item->quiz_count,
                    'moyenne_points' => round($item->avg_points, 2),
                ];
            })->filter()->values();

            return response()->json([
                'formation' => [
                    'id' => $formation->id,
                    'titre' => $formation->titre,
                    'description' => $formation->description,
                    'total_quiz' => count($quizIds),
                ],
                'classement' => $classement,
                'total_participants' => $classement->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération du classement',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtenir le rang du stagiaire connecté dans une formation spécifique
     *
     * @param int $formationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyRanking($formationId)
    {
        try {
            $user = auth()->user();
            
            if (!$user || !$user->stagiaire) {
                return response()->json([
                    'error' => 'Stagiaire non trouvé',
                ], 404);
            }

            $stagiaire = $user->stagiaire;
            $formation = Formation::with('quizzes')->findOrFail($formationId);
            $quizIds = $formation->quizzes->pluck('id')->toArray();

            if (empty($quizIds)) {
                return response()->json([
                    'formation_id' => $formationId,
                    'has_participation' => false,
                    'message' => 'Aucun quiz disponible dans cette formation',
                ]);
            }

            // Calculer les points du stagiaire pour cette formation
            $myStat = Classement::whereIn('quiz_id', $quizIds)
                ->where('stagiaire_id', $stagiaire->id)
                ->select([
                    DB::raw('SUM(points) as total_points'),
                    DB::raw('COUNT(DISTINCT quiz_id) as quiz_count'),
                ])
                ->first();

            if (!$myStat || $myStat->total_points === null) {
                return response()->json([
                    'formation_id' => $formationId,
                    'has_participation' => false,
                    'message' => 'Vous n\'avez pas encore participé à cette formation',
                ]);
            }

            // Calculer le rang en comptant combien de stagiaires ont plus de points
            $betterParticipants = Classement::whereIn('quiz_id', $quizIds)
                ->select('stagiaire_id', DB::raw('SUM(points) as total_points'))
                ->groupBy('stagiaire_id')
                ->having('total_points', '>', $myStat->total_points)
                ->count();

            $myRank = $betterParticipants + 1;

            // Obtenir le total de participants
            $totalParticipants = Classement::whereIn('quiz_id', $quizIds)
                ->distinct('stagiaire_id')
                ->count('stagiaire_id');

            return response()->json([
                'formation_id' => $formationId,
                'formation_titre' => $formation->titre,
                'has_participation' => true,
                'rang' => $myRank,
                'total_points' => (int) $myStat->total_points,
                'quiz_completes' => (int) $myStat->quiz_count,
                'total_quiz' => count($quizIds),
                'total_participants' => $totalParticipants,
                'pourcentage_progression' => round(($myStat->quiz_count / count($quizIds)) * 100, 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération de votre classement',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtenir les formations avec le classement sommaire
     * Utile pour afficher un aperçu sur la liste des formations
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFormationsWithTopRanking()
    {
        try {
            $formations = Formation::with('quizzes')->get();

            $formationsWithRanking = $formations->map(function ($formation) {
                $quizIds = $formation->quizzes->pluck('id')->toArray();

                if (empty($quizIds)) {
                    return [
                        'id' => $formation->id,
                        'titre' => $formation->titre,
                        'has_ranking' => false,
                        'top_3' => [],
                    ];
                }

                // Top 3 du classement
                $top3 = Classement::whereIn('quiz_id', $quizIds)
                    ->select([
                        'stagiaire_id',
                        DB::raw('SUM(points) as total_points'),
                        DB::raw('COUNT(DISTINCT quiz_id) as quiz_count'),
                    ])
                    ->groupBy('stagiaire_id')
                    ->orderByDesc('total_points')
                    ->limit(3)
                    ->get()
                    ->map(function ($item, $index) {
                        $stagiaire = \App\Models\Stagiaire::with('user')->find($item->stagiaire_id);
                        
                        return [
                            'rang' => $index + 1,
                            'nom_complet' => $stagiaire->user->prenom . ' ' . $stagiaire->user->nom,
                            'total_points' => (int) $item->total_points,
                        ];
                    });

                return [
                    'id' => $formation->id,
                    'titre' => $formation->titre,
                    'has_ranking' => $top3->isNotEmpty(),
                    'top_3' => $top3,
                ];
            });

            return response()->json([
                'formations' => $formationsWithRanking,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des formations',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
