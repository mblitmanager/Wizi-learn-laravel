<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\QuizeRepository;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Questions;
use App\Models\Reponse;
use App\Models\Progression;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Stagiaire;
use App\Models\Classement;

class QuizController extends Controller
{
    protected $quizRepository;

    public function __construct(QuizeRepository $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    public function getQuizzesByCategory($category)
    {
        try {
            // Récupérer les quizzes des formations ayant la catégorie spécifiée et qui ont des stagiaires
            $quizzes = Quiz::with(['questions.reponses', 'formation'])
                ->whereHas('formation', function($query) use ($category) {
                    $query->where('categorie', $category)
                          ->whereHas('stagiaires', function($query) {
                              $query->where('role', 'stagiaire');
                          });
                })
                ->get();

            // Transformer les données pour correspondre au format TypeScript
            $formattedQuizzes = $quizzes->map(function($quiz) {
                return [
                    'id' => (string)$quiz->id,
                    'titre' => $quiz->titre,
                    'description' => $quiz->description,
                    'categorie' => $quiz->formation->categorie ?? 'Non catégorisé',
                    'categorieId' => $quiz->formation->categorie ?? 'non-categorise',
                    'niveau' => $quiz->niveau ?? 'débutant',
                    'questions' => $quiz->questions->map(function($question) {
                        $questionData = [
                            'id' => (string)$question->id,
                            'text' => $question->text,
                            'type' => $question->type ?? 'multiplechoice',
                        ];

                        // Gestion spécifique selon le type de question
                        switch ($question->type) {
                            case 'multiplechoice':
                            case 'truefalse':
                                $questionData['answers'] = $question->reponses->map(function($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'isCorrect' => (bool)$reponse->is_correct
                                    ];
                                })->toArray();
                                break;

                            case 'ordering':
                                $questionData['answers'] = $question->reponses->map(function($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'position' => (int)$reponse->position
                                    ];
                                })->sortBy('position')->values()->toArray();
                                break;

                            case 'fillblank':
                                $questionData['blanks'] = $question->reponses->map(function($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'bankGroup' => $reponse->bank_group
                                    ];
                                })->toArray();
                                break;

                            case 'wordbank':
                                $questionData['wordbank'] = $question->reponses->map(function($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'isCorrect' => (bool)$reponse->is_correct,
                                        'bankGroup' => $reponse->bank_group
                                    ];
                                })->toArray();
                                break;

                            case 'flashcard':
                                $questionData['flashcard'] = [
                                    'front' => $question->text,
                                    'back' => $question->flashcard_back
                                ];
                                break;

                            case 'matching':
                                $questionData['matching'] = $question->reponses->map(function($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'matchPair' => $reponse->match_pair
                                    ];
                                })->toArray();
                                break;

                            case 'audioquestion':
                                $questionData['audioUrl'] = $question->audio_url;
                                $questionData['answers'] = $question->reponses->map(function($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'isCorrect' => (bool)$reponse->is_correct
                                    ];
                                })->toArray();
                                break;
                        }

                        return $questionData;
                    })->toArray(),
                    'points' => (int)($quiz->nb_points_total ?? 0)
                ];
            });

            return response()->json($formattedQuizzes);
        } catch (\Exception $e) {
            Log::error('Erreur dans getQuizzesByCategory', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des quizzes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getCategories()
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = Auth::user();

            // Récupérer le stagiaire associé à l'utilisateur
            $stagiaire = Stagiaire::where('user_id', $user->id)->first();

            if (!$stagiaire) {
                return response()->json([
                    'error' => 'Aucun stagiaire associé à cet utilisateur',
                ], 404);
            }

            // Récupérer les catégories uniques depuis les formations associées aux quizzes du stagiaire
            $categories = Quiz::with('formation')
                ->whereHas('formation.stagiaires', function($query) use ($stagiaire) {
                    $query->where('stagiaires.id', $stagiaire->id);
                })
                ->select('formation_id')
                ->distinct()
                ->get()
                ->map(function($quiz) use ($stagiaire) {
                    return [
                        'id' => $quiz->formation->categorie ?? 'non-categorise',
                        'name' => $quiz->formation->categorie ?? 'Non catégorisé',
                        'color' => $this->getCategoryColor($quiz->formation->categorie ?? 'Non catégorisé'),
                        'icon' => $this->getCategoryIcon($quiz->formation->categorie ?? 'Non catégorisé'),
                        'description' => $this->getCategoryDescription($quiz->formation->categorie ?? 'Non catégorisé'),
                        'quizCount' => Quiz::where('formation_id', $quiz->formation_id)
                            ->whereHas('formation.stagiaires', function($query) use ($stagiaire) {
                                $query->where('stagiaires.id', $stagiaire->id);
                            })
                            ->count(),
                        'colorClass' => 'category-' . strtolower(str_replace(' ', '-', $quiz->formation->categorie ?? 'non-categorise'))
                    ];
                })
                ->unique('id')
                ->values();

            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Erreur dans getCategories', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des catégories',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Méthodes utilitaires pour les catégories
    private function getCategoryColor($category)
    {
        $colors = [
            'Bureautique' => '#3D9BE9',
            'Langues' => '#A55E6E',
            'Internet' => '#FFC533',
            'Création' => '#9392BE'
        ];
        return $colors[$category] ?? '#000000';
    }

    private function getCategoryIcon($category)
    {
        $icons = [
            'Bureautique' => 'file-text',
            'Langues' => 'message-square',
            'Internet' => 'globe',
            'Création' => 'palette'
        ];
        return $icons[$category] ?? 'folder';
    }

    private function getCategoryDescription($category)
    {
        $descriptions = [
            'Bureautique' => 'Maîtrisez les outils de bureautique essentiels',
            'Langues' => 'Améliorez vos compétences linguistiques',
            'Internet' => 'Découvrez le monde du web et des réseaux sociaux',
            'Création' => 'Explorez les outils de création graphique'
        ];
        return $descriptions[$category] ?? '';
    }

    public function getQuizHistory()
    {
        $user = Auth::user();
        $stagiaire = Stagiaire::where('user_id', $user->id)->first();

        if (!$stagiaire) {
            return response()->json([], 200);
        }

        $history = Progression::with(['quiz' => function($query) {
            $query->with(['questions.reponses']);
        }])
        ->where('stagiaire_id', $stagiaire->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($progression) {
            return [
                'id' => (string)$progression->id,
                'quiz' => [
                    'id' => (string)$progression->quiz->id,
                    'title' => $progression->quiz->titre,
                    'category' => $progression->quiz->formation->categorie ?? 'Non catégorisé',
                    'level' => $progression->quiz->niveau ?? 'débutant'
                ],
                'score' => $progression->score,
                'completedAt' => $progression->created_at->toISOString(),
                'timeSpent' => $progression->time_spent
            ];
        });

        return response()->json($history);
    }

    public function getQuizStats()
    {
        $user = Auth::user();
        $stagiaire = Stagiaire::where('user_id', $user->id)->first();

        if (!$stagiaire) {
            return response()->json([
                'totalQuizzes' => 0,
                'averageScore' => 0,
                'totalPoints' => 0,
                'categoryStats' => [],
                'levelProgress' => []
            ]);
        }

        $stats = [
            'totalQuizzes' => Progression::where('stagiaire_id', $stagiaire->id)->count(),
            'averageScore' => Progression::where('stagiaire_id', $stagiaire->id)->avg('score'),
            'totalPoints' => Progression::where('stagiaire_id', $stagiaire->id)->sum('score'),
            'categoryStats' => $this->getCategoryStats($stagiaire->id),
            'levelProgress' => $this->getLevelProgress($stagiaire->id)
        ];

        return response()->json($stats);
    }

    public function getQuizStatistics($quizId)
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = Auth::user();
            $stagiaire = Stagiaire::where('user_id', $user->id)->first();

            if (!$stagiaire) {
                return response()->json([
                    'error' => 'Aucun stagiaire associé à cet utilisateur',
                ], 404);
            }

            // Récupérer les statistiques du quiz
            $quiz = Quiz::findOrFail($quizId);
            $progressions = Progression::where('quiz_id', $quizId)
                ->where('stagiaire_id', $stagiaire->id)
                ->get();

            $totalAttempts = $progressions->count();
            $averageScore = $totalAttempts > 0 ? $progressions->avg('score') : 0;
            $bestScore = $totalAttempts > 0 ? $progressions->max('score') : 0;
            $lastAttempt = $totalAttempts > 0 ? $progressions->last() : null;

            $statistics = [
                'total_attempts' => $totalAttempts,
                'average_score' => round($averageScore, 2),
                'best_score' => $bestScore,
                'last_attempt' => $lastAttempt ? [
                    'score' => $lastAttempt->score,
                    'date' => $lastAttempt->created_at->format('Y-m-d H:i:s'),
                    'time_spent' => $lastAttempt->time_spent
                ] : null,
                'quiz' => [
                    'id' => $quiz->id,
                    'title' => $quiz->titre,
                    'total_questions' => $quiz->questions->count(),
                    'total_points' => $quiz->nb_points_total
                ]
            ];

            return response()->json($statistics);
        } catch (\Exception $e) {
            Log::error('Erreur dans getQuizStatistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des statistiques du quiz',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getCategoryStats($stagiaireId)
    {
        // Récupérer toutes les progressions du stagiaire
        $progressions = Progression::where('stagiaire_id', $stagiaireId)
            ->with('quiz.formation')
            ->get();

        // Grouper les progressions par catégorie
        $categoryStats = $progressions->groupBy(function($progression) {
            return $progression->quiz->formation->categorie ?? 'Non catégorisé';
        })->map(function($group) {
            return [
                'category' => $group->first()->quiz->formation->categorie ?? 'Non catégorisé',
                'quizCount' => $group->count(),
                'averageScore' => $group->avg('score')
            ];
        })->values();

        return $categoryStats;
    }

    private function getLevelProgress($stagiaireId)
    {
        $progressions = Progression::where('stagiaire_id', $stagiaireId)
            ->with('quiz')
            ->get();

        $levels = ['débutant', 'intermédiaire', 'avancé'];
        $levelStats = [];

        foreach ($levels as $level) {
            $levelQuizzes = $progressions->filter(function($progression) use ($level) {
                return $progression->quiz->niveau === $level;
            });

            $levelStats[$level] = [
                'completed' => $levelQuizzes->count(),
                'averageScore' => $levelQuizzes->avg('score')
            ];
        }

        return $levelStats;
    }

    public function getQuizById($id)
    {
        try {
            $quiz = Quiz::with(['questions.reponses', 'formation'])
                ->findOrFail($id);

            return response()->json([
                'id' => (string)$quiz->id,
                'titre' => $quiz->titre,
                'description' => $quiz->description,
                'categorie' => $quiz->formation->categorie ?? 'Non catégorisé',
                'categorieId' => $quiz->formation->categorie ?? 'non-categorise',
                'niveau' => $quiz->niveau ?? 'débutant',
                'questions' => $quiz->questions->map(function($question) {
                    $questionData = [
                        'id' => (string)$question->id,
                        'text' => $question->text,
                        'type' => $question->type ?? 'multiplechoice',
                    ];

                    // Par défaut, toutes les questions ont des réponses
                    $questionData['answers'] = $question->reponses->map(function($reponse) {
                        return [
                            'id' => (string)$reponse->id,
                            'text' => $reponse->text,
                            'isCorrect' => (bool)$reponse->is_correct
                        ];
                    })->toArray();

                    // Ajout des propriétés spécifiques selon le type
                    switch ($question->type) {
                        case 'ordering':
                            $questionData['answers'] = $question->reponses->map(function($reponse) {
                                return [
                                    'id' => (string)$reponse->id,
                                    'text' => $reponse->text,
                                    'position' => (int)$reponse->position
                                ];
                            })->sortBy('position')->values()->toArray();
                            break;

                        case 'fillblank':
                            $questionData['blanks'] = $question->reponses->map(function($reponse) {
                                return [
                                    'id' => (string)$reponse->id,
                                    'text' => $reponse->text,
                                    'bankGroup' => $reponse->bank_group
                                ];
                            })->toArray();
                            break;

                        case 'wordbank':
                            $questionData['wordbank'] = $question->reponses->map(function($reponse) {
                                return [
                                    'id' => (string)$reponse->id,
                                    'text' => $reponse->text,
                                    'isCorrect' => (bool)$reponse->is_correct,
                                    'bankGroup' => $reponse->bank_group
                                ];
                            })->toArray();
                            break;

                        case 'flashcard':
                            $questionData['flashcard'] = [
                                'front' => $question->text,
                                'back' => $question->flashcard_back
                            ];
                            break;

                        case 'matching':
                            $questionData['matching'] = $question->reponses->map(function($reponse) {
                                return [
                                    'id' => (string)$reponse->id,
                                    'text' => $reponse->text,
                                    'matchPair' => $reponse->match_pair
                                ];
                            })->toArray();
                            break;

                        case 'audioquestion':
                            $questionData['audioUrl'] = $question->audio_url;
                            break;
                    }

                    return $questionData;
                })->toArray(),
                'points' => (int)($quiz->nb_points_total ?? 0)
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans getQuizById', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Quiz non trouvé',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function submitQuizResult(Request $request, $id)
    {
        $user = null;
        try {
            $user = Auth::user();
            $quiz = Quiz::with(['formation', 'questions.reponses'])->findOrFail($id);

            // Log de débogage détaillé
            Log::info('Requête de soumission de quiz', [
                'request_data' => $request->all(),
                'request_answers' => $request->answers,
                'quiz_id' => $id,
                'user_id' => $user->id
            ]);

            // Validation des données
            $validated = $request->validate([
                'answers' => 'required|array',
                'timeSpent' => 'required|integer|min:0'
            ]);

            // Récupérer le stagiaire associé à l'utilisateur
            $stagiaire = Stagiaire::where('user_id', $user->id)->firstOrFail();

            // Préparer les détails des questions et réponses
            $questionsDetails = $quiz->questions->map(function($question) use ($request) {
                // S'assurer que les réponses sont dans un tableau
                $selectedAnswerIds = is_array($request->answers[$question->id] ?? null)
                    ? $request->answers[$question->id]
                    : [];

                // Log de débogage pour chaque question
                Log::info('Traitement de la question', [
                    'question_id' => $question->id,
                    'selected_answers' => $selectedAnswerIds,
                    'request_answers' => $request->answers,
                    'question_data' => $question->toArray()
                ]);

                $correctAnswerIds = $question->reponses->where('is_correct', true)->pluck('id')->toArray();

                return [
                    'id' => $question->id,
                    'text' => $question->text,
                    'type' => $question->type,
                    'selectedAnswers' => $selectedAnswerIds,
                    'correctAnswers' => $correctAnswerIds,
                    'answers' => $question->reponses->map(function($reponse) {
                        return [
                            'id' => $reponse->id,
                            'text' => $reponse->text,
                            'isCorrect' => $reponse->is_correct
                        ];
                    })->toArray(),
                    'isCorrect' => empty(array_diff($selectedAnswerIds, $correctAnswerIds)) &&
                                 empty(array_diff($correctAnswerIds, $selectedAnswerIds))
                ];
            });

            // Calculer le score et le nombre de réponses correctes
            $correctAnswers = $questionsDetails->where('isCorrect', true)->count();
            $totalQuestions = $questionsDetails->count();
            $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

            $result = Progression::create([
                'stagiaire_id' => $stagiaire->id,
                'quiz_id' => $quiz->id,
                'formation_id' => $quiz->formation->id,
                'score' => $score,
                'correct_answers' => $correctAnswers,
                'total_questions' => $totalQuestions,
                'time_spent' => $request->timeSpent,
                'completion_time' => now()
            ]);

            // Mettre à jour le classement
            $this->updateClassement($quiz->id, $stagiaire->id, $score);

            return response()->json([
                'id' => $result->id,
                'quizId' => $result->quiz_id,
                'stagiaireId' => $result->stagiaire_id,
                'formationId' => $result->formation_id,
                'score' => $result->score,
                'correctAnswers' => $result->correct_answers,
                'totalQuestions' => $result->total_questions,
                'completedAt' => $result->completion_time->toISOString(),
                'timeSpent' => $result->time_spent,
                'questions' => $questionsDetails
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur dans submitQuizResult', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user?->id,
                'quiz_id' => $id,
                'formation_id' => $quiz->formation->id ?? null,
                'request_data' => $request->all(),
                'validation_errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : null
            ]);

            return response()->json([
                'error' => 'Erreur lors de la sauvegarde du résultat',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function updateClassement($quizId, $stagiaireId, $score)
    {
        try {
            // Vérifier si le stagiaire a déjà un classement pour ce quiz
            $classement = Classement::where('quiz_id', $quizId)
                ->where('stagiaire_id', $stagiaireId)
                ->first();

            if ($classement) {
                // Mettre à jour le score si le nouveau score est meilleur
                if ($score > $classement->points) {
                    $classement->update(['points' => $score]);
                }
            } else {
                // Créer un nouveau classement
                $classement = Classement::create([
                    'quiz_id' => $quizId,
                    'stagiaire_id' => $stagiaireId,
                    'points' => $score
                ]);
            }

            // Mettre à jour les rangs pour ce quiz
            $this->updateRanks($quizId);

            return $classement;
        } catch (\Exception $e) {
            Log::error('Erreur dans updateClassement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function updateRanks($quizId)
    {
        // Récupérer tous les classements pour ce quiz, triés par points décroissants
        $classements = Classement::where('quiz_id', $quizId)
            ->orderBy('points', 'desc')
            ->get();

        // Mettre à jour les rangs
        $rank = 1;
        foreach ($classements as $classement) {
            $classement->update(['rang' => $rank++]);
        }
    }

    public function getStagiaireQuizzes()
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = Auth::user();

            // Récupérer le stagiaire associé à l'utilisateur
            $stagiaire = Stagiaire::where('user_id', $user->id)->first();

            if (!$stagiaire) {
                return response()->json([
                    'error' => 'Aucun stagiaire associé à cet utilisateur',
                ], 404);
            }

            // Récupérer les quizzes associés aux formations du stagiaire
            $quizzes = Quiz::select('quizzes.*')
                ->join('formations', 'quizzes.formation_id', '=', 'formations.id')
                ->join('stagiaire_formations', 'formations.id', '=', 'stagiaire_formations.formation_id')
                ->where('stagiaire_formations.stagiaire_id', $stagiaire->id)
                ->with(['formation', 'questions.reponses'])
                ->get()
                ->map(function($quiz) {
                    return [
                        'id' => (string)$quiz->id,
                        'titre' => $quiz->titre,
                        'description' => $quiz->description,
                        'categorie' => $quiz->formation->categorie ?? 'Non catégorisé',
                        'categorieId' => $quiz->formation->categorie ?? 'non-categorise',
                        'niveau' => $quiz->niveau ?? 'débutant',
                        'questions' => $quiz->questions->map(function($question) {
                            return [
                                'id' => (string)$question->id,
                                'text' => $question->text,
                                'type' => $question->type ?? 'multiplechoice',
                                'answers' => $question->reponses->map(function($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'isCorrect' => (bool)$reponse->is_correct
                                    ];
                                })->toArray()
                            ];
                        })->toArray(),
                        'points' => (int)($quiz->nb_points_total ?? 0)
                    ];
                });

            return response()->json($quizzes);
        } catch (\Exception $e) {
            Log::error('Erreur dans getStagiaireQuizzes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des quizzes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getClassement($quizId)
    {
        try {
            $classement = Classement::with(['stagiaire.user', 'quiz'])
                ->where('quiz_id', $quizId)
                ->orderBy('rang')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => (string)$item->id,
                        'rang' => $item->rang,
                        'points' => $item->points,
                        'stagiaire' => [
                            'id' => (string)$item->stagiaire->id,
                            'prenom' => $item->stagiaire->prenom,
                            'image' => $item->stagiaire->user->image ?? null
                        ],
                        'quiz' => [
                            'id' => (string)$item->quiz->id,
                            'titre' => $item->quiz->titre
                        ]
                    ];
                });

            return response()->json($classement);
        } catch (\Exception $e) {
            Log::error('Erreur dans getClassement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération du classement',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getGlobalClassement()
    {
        try {
            // Récupérer tous les classements avec leurs relations
            $classements = Classement::with(['stagiaire.user', 'quiz'])
                ->get()
                ->groupBy('stagiaire_id')
                ->map(function($group) {
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
                ->values()
                ->map(function($item, $index) {
                    return [
                        ...$item,
                        'rang' => $index + 1
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
}
