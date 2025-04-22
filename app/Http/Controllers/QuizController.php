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
            // Récupérer les catégories uniques depuis les formations associées aux quizzes
            $categories = Quiz::with('formation')
                ->select('formation_id')
                ->distinct()
                ->get()
                ->map(function($quiz) {
                    return [
                        'id' => $quiz->formation->categorie ?? 'non-categorise',
                        'name' => $quiz->formation->categorie ?? 'Non catégorisé',
                        'color' => $this->getCategoryColor($quiz->formation->categorie ?? 'Non catégorisé'),
                        'icon' => $this->getCategoryIcon($quiz->formation->categorie ?? 'Non catégorisé'),
                        'description' => $this->getCategoryDescription($quiz->formation->categorie ?? 'Non catégorisé'),
                        'quizCount' => Quiz::where('formation_id', $quiz->formation_id)->count(),
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

        $history = Progression::with(['quiz' => function($query) {
            $query->with(['questions.answers']);
        }])
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($progression) {
            return [
                'id' => (string)$progression->id,
                'quiz' => [
                    'id' => (string)$progression->quiz->id,
                    'title' => $progression->quiz->title,
                    'category' => $progression->quiz->category,
                    'level' => $progression->quiz->level
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

        $stats = [
            'totalQuizzes' => Progression::where('user_id', $user->id)->count(),
            'averageScore' => Progression::where('user_id', $user->id)->avg('score'),
            'totalPoints' => Progression::where('user_id', $user->id)->sum('score'),
            'categoryStats' => $this->getCategoryStats($user->id),
            'levelProgress' => $this->getLevelProgress($user->id)
        ];

        return response()->json($stats);
    }

    private function getCategoryStats($userId)
    {
        return Quiz::select('category', 'category_id')
            ->withCount(['progressions' => function($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->with(['progressions' => function($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->get()
            ->map(function($quiz) {
                return [
                    'category' => $quiz->category,
                    'quizCount' => $quiz->progressions_count,
                    'averageScore' => $quiz->progressions->avg('score')
                ];
            });
    }

    private function getLevelProgress($userId)
    {
        $progressions = Progression::where('user_id', $userId)
            ->with('quiz')
            ->get();

        $levels = ['débutant', 'intermédiaire', 'avancé'];
        $levelStats = [];

        foreach ($levels as $level) {
            $levelQuizzes = $progressions->filter(function($progression) use ($level) {
                return $progression->quiz->level === $level;
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

            // Log de débogage
            Log::info('Requête de soumission de quiz', [
                'request_data' => $request->all(),
                'quiz_id' => $id,
                'user_id' => $user->id
            ]);

            // Validation des données
            $request->validate([
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
                    'request_answers' => $request->answers
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
                'request_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la sauvegarde du résultat',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
