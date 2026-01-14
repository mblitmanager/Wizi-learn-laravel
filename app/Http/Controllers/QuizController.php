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
use App\Models\CorrespondancePair;
use App\Models\QuizParticipation;
use App\Models\QuizParticipationAnswer;
use Illuminate\Support\Facades\DB;
use App\Models\QuizCategory;
use App\Models\Formation;
use App\Models\CatalogueFormation;
use App\Models\Participation;
use App\Models\ParticipationAnswer;

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
            $user = Auth::user();
            $stagiaire = Stagiaire::where('user_id', $user->getKey())->first();

            if (!$stagiaire) {
                return response()->json([]);
            }

            // Récupérer les quizzes des formations ayant la catégorie spécifiée et qui sont assignées au stagiaire connecté
            $quizzes = Quiz::select('quizzes.*')
                ->join('formations', 'formations.id', '=', 'quizzes.formation_id')
                ->join('catalogue_formations', 'catalogue_formations.formation_id', '=', 'formations.id')
                ->join('stagiaire_catalogue_formations', 'stagiaire_catalogue_formations.catalogue_formation_id', '=', 'catalogue_formations.id')
                ->where('formations.categorie', $category)
                ->where('stagiaire_catalogue_formations.stagiaire_id', $stagiaire->id)
                ->where('quizzes.status', 'actif')
                ->distinct()
                ->with(['formation'])   // Removed 'questions.reponses'
                ->withCount('questions') // Add count instead
                ->get();

            Log::info('getQuizzesByCategory request (Optimized)', [
                'category_requested' => $category,
                'count' => $quizzes->count(),
            ]);

            // Transformer les données pour correspondre au format TypeScript
            // Format Allégé pour liste
            $formattedQuizzes = $quizzes->map(function ($quiz) {
                return [
                    'id' => (string) $quiz->id,
                    'titre' => $quiz->titre,
                    'description' => \Illuminate\Support\Str::limit($quiz->description, 150),
                    'categorie' => $quiz->formation->categorie ?? 'Non catégorisé',
                    'categorieId' => $quiz->formation->categorie ?? 'non-categorise',
                    'niveau' => $quiz->niveau ?? 'débutant',
                    'questionCount' => $quiz->questions_count, // Use pre-calculated count
                    'questions' => [], // Empty array for list view to save bandwidth
                    'points' => (int) ($quiz->nb_points_total ?? 0)
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
            $stagiaire = Stagiaire::where('user_id', $user->getKey())->first();

            if (!$stagiaire) {
                return response()->json([
                    'error' => 'Aucun stagiaire associé à cet utilisateur',
                ], 404);
            }

            // Récupérer les catégories uniques depuis les formations associées aux quizzes du stagiaire
            // Cache for 24 hours (86400 seconds)
            $cacheKey = "quiz_categories_{$stagiaire->id}";

            $categories = \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function () use ($stagiaire) {
                return Quiz::select('quizzes.*')
                    ->join('formations', 'formations.id', '=', 'quizzes.formation_id')
                    ->join('catalogue_formations', 'catalogue_formations.formation_id', '=', 'formations.id')
                    ->join('stagiaire_catalogue_formations', 'stagiaire_catalogue_formations.catalogue_formation_id', '=', 'catalogue_formations.id')
                    ->where('stagiaire_catalogue_formations.stagiaire_id', $stagiaire->id)
                    ->with(['formation'])
                    ->distinct()
                    ->get()
                    ->map(function ($quiz) use ($stagiaire) {
                        return [
                            'id' => $quiz->formation->categorie ?? 'non-categorise',
                            'name' => $quiz->formation->categorie ?? 'Non catégorisé',
                            'color' => $this->getCategoryColor($quiz->formation->categorie ?? 'Non catégorisé'),
                            'icon' => $this->getCategoryIcon($quiz->formation->categorie ?? 'Non catégorisé'),
                            'description' => $this->getCategoryDescription($quiz->formation->categorie ?? 'Non catégorisé'),
                            'quizCount' => Quiz::select('quizzes.*')
                                ->join('formations', 'formations.id', '=', 'quizzes.formation_id')
                                ->join('catalogue_formations', 'catalogue_formations.formation_id', '=', 'formations.id')
                                ->join('stagiaire_catalogue_formations', 'stagiaire_catalogue_formations.catalogue_formation_id', '=', 'catalogue_formations.id')
                                ->where('stagiaire_catalogue_formations.stagiaire_id', $stagiaire->id)
                                ->where('quizzes.formation_id', $quiz->formation_id)
                                ->count(),
                            'colorClass' => 'category-' . strtolower(str_replace(' ', '-', $quiz->formation->categorie ?? 'non-categorise'))
                        ];
                    })
                    ->unique('id')
                    ->values();
            });

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

        $history = Progression::with([
            'quiz.formation',
            // 'quiz.questions.reponses', // REMOVED: Too heavy for list view
        ])
            ->where('stagiaire_id', $stagiaire->id)
            ->withCount('quiz as total_questions_count') // Approximate, or rely on quiz->nb_questions
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($progression) {
                $quiz = $progression->quiz;
                if (!$quiz) return null; // Handle deleted quizzes safely

                $niveau = $quiz->niveau ?? 'débutant';

                // Use progression's stored total if available, or quiz aggregation
                $totalQuestions = $progression->total_questions ?? $quiz->questions_count ?? 0;

                // Construction d'un objet quiz allégé
                $quizData = [
                    'id' => (int) $quiz->id,
                    'titre' => $quiz->titre,
                    'description' => \Illuminate\Support\Str::limit($quiz->description, 100),
                    'duree' => $quiz->duree,
                    'niveau' => $quiz->niveau,
                    'status' => $quiz->status,
                    'nb_points_total' => $quiz->nb_points_total,
                    'formation' => $quiz->formation ? [
                        'id' => $quiz->formation->id,
                        'titre' => $quiz->formation->titre,
                        'categorie' => $quiz->formation->categorie,
                    ] : null,
                    // Questions removed for performance
                    'questions' => [],
                ];
                return [
                    'id' => (string) $progression->id,
                    'quiz' => $quizData,
                    'score' => $progression->score,
                    'completedAt' => $progression->created_at->toISOString(),
                    'timeSpent' => $progression->time_spent,
                    'totalQuestions' => $totalQuestions,
                    'correctAnswers' => $progression->correct_answers
                ];
            })
            ->filter(); // Remove nulls

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

        // Utiliser Classement pour le calcul du score moyen
        $classements = Classement::where('stagiaire_id', $stagiaire->id)->get();
        $totalPoints = $classements->sum('points');
        $quizCount = $classements->count();
        $averageScore = $quizCount > 0 ? round($totalPoints / $quizCount, 2) : 0;
        $stats = [
            'totalQuizzes' => $quizCount,
            'averageScore' => $averageScore,
            'totalPoints' => $totalPoints,
            'categoryStats' => $this->getCategoryStatsForStagiaire($stagiaire->id),
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

    private function getCategoryStatsForStagiaire($stagiaireId)
    {
        // Récupérer tous les classements du stagiaire avec quiz et formation
        $classements = Classement::where('stagiaire_id', $stagiaireId)
            ->with('quiz.formation')
            ->get();

        // Grouper les classements par catégorie de formation
        $categoryStats = $classements->groupBy(function ($classement) {
            return $classement->quiz->formation->categorie ?? 'Non catégorisé';
        })->map(function ($group) {
            return [
                'category' => $group->first()->quiz->formation->categorie ?? 'Non catégorisé',
                'quizCount' => $group->count(),
                'averageScore' => $group->avg('points')
            ];
        })->values();

        return $categoryStats;
    }

    private function getLevelProgress($stagiaireId)
    {
        // Utiliser Classement pour les stats par niveau
        $classements = Classement::where('stagiaire_id', $stagiaireId)
            ->with('quiz')
            ->get();

        $levels = ['débutant', 'intermédiaire', 'avancé'];
        $levelStats = [];

        foreach ($levels as $level) {
            $levelClassements = $classements->filter(function ($classement) use ($level) {
                return $classement->quiz && $classement->quiz->niveau === $level;
            });

            $levelStats[$level] = [
                'completed' => $levelClassements->count(),
                'averageScore' => $levelClassements->avg('points')
            ];
        }

        return $levelStats;
    }

    public function getQuizById($id)
    {
        try {
            $quiz = Quiz::with(['questions.reponses', 'formation'])
                ->findOrFail($id);

            Log::info('getQuizById request', [
                'id_requested' => $id,
                'quiz_found_id' => $quiz->id,
                'titre' => $quiz->titre,
                'formation_categorie' => $quiz->formation->categorie ?? 'N/A'
            ]);

            return response()->json([
                'id' => (string) $quiz->id,
                'titre' => $quiz->titre,
                'description' => $quiz->description,
                'categorie' => $quiz->formation->categorie ?? 'Non catégorisé',
                'categorieId' => $quiz->formation->categorie ?? 'non-categorise',
                'niveau' => $quiz->niveau ?? 'débutant',
                'questions' => $quiz->questions->map(function ($question) {
                    $questionData = [
                        'id' => (string) $question->id,
                        'text' => $question->text,
                        'type' => $question->type ?? 'choix multiples',
                    ];

                    // Par défaut, toutes les questions ont des réponses
                    $questionData['answers'] = $question->reponses->map(function ($reponse) {
                        return [
                            'id' => (string) $reponse->id,
                            'text' => $reponse->text,
                            'isCorrect' => (bool) $reponse->is_correct
                        ];
                    })->toArray();

                    // Ajout des propriétés spécifiques selon le type
                    switch ($question->type) {
                        case 'rearrangement':
                            $questionData['answers'] = $question->reponses->map(function ($reponse) {
                                return [
                                    'id' => (string) $reponse->id,
                                    'text' => $reponse->text,
                                    'position' => (int) $reponse->position
                                ];
                            })->sortBy('position')->values()->toArray();
                            break;

                        case 'remplir le champ vide':
                            $questionData['blanks'] = $question->reponses->map(function ($reponse) {
                                return [
                                    'id' => (string) $reponse->id,
                                    'text' => $reponse->text,
                                    'bankGroup' => $reponse->bank_group
                                ];
                            })->toArray();
                            break;

                        case 'banque de mots':
                            $questionData['wordbank'] = $question->reponses->map(function ($reponse) {
                                return [
                                    'id' => (string) $reponse->id,
                                    'text' => $reponse->text,
                                    'isCorrect' => (bool) $reponse->is_correct,
                                    'bankGroup' => $reponse->bank_group
                                ];
                            })->toArray();
                            break;

                        case 'carte flash':
                            $questionData['flashcard'] = [
                                'front' => $question->text,
                                'back' => $question->flashcard_back
                            ];
                            break;

                        case 'correspondance':
                            $questionData['matching'] = $question->reponses->map(function ($reponse) {
                                return [
                                    'id' => (string) $reponse->id,
                                    'text' => $reponse->text,
                                    'matchPair' => $reponse->match_pair
                                ];
                            })->toArray();
                            break;

                        case 'question audio':
                            $questionData['audioUrl'] = $question->audio_url ?? $question->media_url ?? null;
                            break;
                    }

                    return $questionData;
                })->toArray(),
                'points' => (int) ($quiz->nb_points_total ?? 0)
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

    private function isAnswerCorrect($question, $selectedAnswers)
    {
        // Fonction pour normaliser et filtrer les valeurs
        $normalize = function ($value) {
            if (is_array($value)) {
                return array_filter($value, fn($v) => $v !== null && $v !== '' && $v !== []);
            }
            return $value !== null && $value !== '' ? $value : null;
        };

        // Nettoyer les réponses sélectionnées
        $cleanedSelectedAnswers = $normalize($selectedAnswers);

        // Récupérer les bonnes réponses une seule fois pour les types qui l'utilisent
        $correctAnswers = $question->reponses
            ->where('is_correct', true)
            ->pluck('text')
            ->toArray();

        switch ($question->type) {
            case 'correspondance':
                $reponsesMap = $question->reponses->pluck('text', 'id')->toArray();
                $selectedPairs = [];

                foreach ($selectedAnswers as $leftId => $rightText) {
                    if (isset($reponsesMap[$leftId]) && !empty($rightText)) {
                        $selectedPairs[$reponsesMap[$leftId]] = $rightText;
                    }
                }

                $correctPairs = CorrespondancePair::where('question_id', $question->id)
                    ->whereIn('left_text', $question->reponses->pluck('text')->toArray())
                    ->get()
                    ->mapWithKeys(fn($pair) => [$pair->left_text => $pair->right_text])
                    ->toArray();

                $matchPairs = CorrespondancePair::where('question_id', $question->id)
                    ->whereIn('left_text', $question->reponses->pluck('text')->toArray())
                    ->get()
                    ->map(fn($pair) => ['left' => $pair->left_text, 'right' => $pair->right_text]);

                return [
                    'selectedAnswers' => $selectedPairs,
                    'correctAnswers' => $correctPairs,
                    'isCorrect' => $selectedPairs == $correctPairs,
                    'match_pair' => $matchPairs
                ];

            case 'carte flash':
            case 'question audio':
                $selectedText = is_array($cleanedSelectedAnswers)
                    ? ($cleanedSelectedAnswers['text'] ?? (count($cleanedSelectedAnswers) > 0 ? $cleanedSelectedAnswers[0] : null))
                    : (is_object($cleanedSelectedAnswers) ? $cleanedSelectedAnswers->text : $cleanedSelectedAnswers);

                return [
                    'selectedAnswers' => $selectedText,
                    'correctAnswers' => $correctAnswers,
                    'isCorrect' => !empty($selectedText) && in_array($selectedText, $correctAnswers)
                ];

            case 'remplir le champ vide':
                $correctBlanks = [];
                foreach ($question->reponses as $reponse) {
                    if ($reponse->bank_group && $reponse->is_correct) {
                        $correctBlanks[$reponse->bank_group] = $normalize($reponse->text);
                    }
                }

                $details = [
                    'selectedAnswers' => $cleanedSelectedAnswers,
                    'correctAnswers' => $correctBlanks,
                    'isCorrect' => false
                ];

                if (is_array($cleanedSelectedAnswers)) {
                    // Cas avec des bank_groups
                    if (array_keys($cleanedSelectedAnswers) !== range(0, count($cleanedSelectedAnswers) - 1)) {
                        $allCorrect = true;
                        foreach ($correctBlanks as $blankId => $correctText) {
                            $userText = $normalize($cleanedSelectedAnswers[$blankId] ?? null);
                            if ($userText !== $correctText) {
                                $allCorrect = false;
                                break;
                            }
                        }
                        $details['isCorrect'] = $allCorrect &&
                            count(array_intersect_key($cleanedSelectedAnswers, $correctBlanks)) === count($correctBlanks);
                    }
                    // Cas tableau simple
                    else {
                        $correctTexts = array_map($normalize, array_values($correctBlanks));
                        $userTexts = array_map($normalize, array_values($cleanedSelectedAnswers));
                        $details['isCorrect'] = $userTexts === $correctTexts;
                        $details['correctAnswers'] = $correctTexts;
                    }
                }
                return $details;

            case 'choix multiples':
            case 'banque de mots':
            case 'vrai/faux':
                $selected = is_array($cleanedSelectedAnswers)
                    ? array_values($cleanedSelectedAnswers)
                    : [$cleanedSelectedAnswers];

                return [
                    'selectedAnswers' => $selected,
                    'correctAnswers' => $correctAnswers,
                    'isCorrect' => empty(array_diff($selected, $correctAnswers)) &&
                        empty(array_diff($correctAnswers, $selected))
                ];

            case 'rearrangement':
                $correctOrder = $question->reponses
                    ->where('is_correct', true)
                    ->sortBy('position')
                    ->pluck('text')
                    ->values()
                    ->toArray();

                $userOrder = is_array($cleanedSelectedAnswers) ? array_values($cleanedSelectedAnswers) : [];
                return [
                    'selectedAnswers' => $userOrder,
                    'correctAnswers' => $correctOrder,
                    'isCorrect' => $userOrder === $correctOrder
                ];

            default:
                // Comparaison standard
                if (is_array($cleanedSelectedAnswers)) {
                    return [
                        'selectedAnswers' => $cleanedSelectedAnswers,
                        'correctAnswers' => $correctAnswers,
                        'isCorrect' => empty(array_diff($cleanedSelectedAnswers, $correctAnswers)) &&
                            empty(array_diff($correctAnswers, $cleanedSelectedAnswers))
                    ];
                }
                return [
                    'selectedAnswers' => $cleanedSelectedAnswers,
                    'correctAnswers' => $correctAnswers,
                    'isCorrect' => in_array($cleanedSelectedAnswers, $correctAnswers)
                ];
        }
    }

    public function submitQuizResult(Request $request, $id)
    {
        $user = null;
        try {
            $user = Auth::user();
            $quiz = Quiz::with(['formation', 'questions.reponses'])->findOrFail($id);

            // Log complet pour le débogage
            Log::info('Début du traitement du quiz', [
                'quiz_id' => $quiz->id,
                'questions_in_quiz' => $quiz->questions->pluck('id'),
                'questions_in_request' => array_keys($request->answers)
            ]);

            // Validation
            $validated = $request->validate([
                'answers' => 'required|array',
                'timeSpent' => 'required|integer|min:0'
            ]);

            // Log de débogage détaillé - Payload complet
            Log::info('Requête de soumission de quiz', [
                'request_data' => $request->all(),
                'request_answers' => $request->answers,
                'quiz_id' => $id,
                'user_id' => $user->getKey()
            ]);

            // Log de tous les types de questions du quiz
            Log::info('Types de questions du quiz', [
                'questions' => $quiz->questions->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'text' => $q->text,
                        'type' => $q->type
                    ];
                })
            ]);

            // Validation des données
            $validated = $request->validate([
                'answers' => 'required|array',
                'timeSpent' => 'required|integer|min:0'
            ]);

            // Récupérer le stagiaire associé à l'utilisateur
            $stagiaire = Stagiaire::where('user_id', $user->getKey())->firstOrFail();

            // Récupérer ou créer la participation en cours
            $participation = QuizParticipation::firstOrCreate(
                [
                    'user_id' => $user->getKey(),
                    'quiz_id' => $quiz->id,
                    'status' => 'in_progress'
                ],
                [
                    'started_at' => now(),
                    'score' => 0,
                    'correct_answers' => 0,
                    'time_spent' => 0
                ]
            );

            // Préparer les détails des questions et réponses
            $questionsDetails = $quiz->questions
                ->filter(function ($question) use ($request) {
                    return isset($request->answers[$question->id]) && $request->answers[$question->id] !== null;
                })
                ->map(function ($question) use ($request) {

                    $selectedAnswers = $request->answers[$question->id] ?? null;
                    $isCorrectResult = $this->isAnswerCorrect($question, $selectedAnswers);

                    return [
                        'id' => $question->id,
                        'text' => $question->text,
                        'type' => $question->type,
                        'selectedAnswers' => $isCorrectResult['selectedAnswers'] ?? [],
                        'correctAnswers' => $isCorrectResult['correctAnswers'] ?? [],
                        'answers' => $question->reponses->map(function ($reponse) {
                            return [
                                'id' => $reponse->id,
                                'text' => $reponse->text,
                                'isCorrect' => $reponse->is_correct
                            ];
                        })->toArray(),
                        'isCorrect' => $isCorrectResult['isCorrect'] ?? false,
                        'meta' => $question->type === 'correspondance'
                            ? [
                                'selectedAnswers' => $isCorrectResult['selectedAnswers'],
                                'correctAnswers' => $isCorrectResult['correctAnswers'],
                                'isCorrect' => $isCorrectResult['isCorrect'],
                                'match_pair' => $isCorrectResult['match_pair']
                            ]
                            : null
                    ];
                });

            $correctAnswers = $questionsDetails->where('isCorrect', true)->count();
            $totalQuestions = $questionsDetails->count();
            $score = $correctAnswers * 2;

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

            // TRAITEMENT DES ANSWERS
            foreach ($request->answers as $questionId => $answerValue) {
                Log::info('Traitement de la réponse utilisateur', [
                    'question_id' => $questionId,
                    'answer_value' => $answerValue
                ]);

                $question = $quiz->questions->firstWhere('id', $questionId);

                if (!$question) {
                    Log::warning('Question non trouvée dans le quiz', [
                        'question_id' => $questionId
                    ]);
                    continue;
                }

                Log::info('Type de la question à traiter', [
                    'question_id' => $questionId,
                    'question_type' => $question->type
                ]);

                try {
                    if ($question->type === 'correspondance') {
                        if (!is_array($answerValue) && !is_object($answerValue)) {
                            Log::warning('Réponse de correspondance invalide (non itérable)', [
                                'question_id' => $questionId,
                                'answer_value' => $answerValue
                            ]);
                            continue;
                        }

                        $answerValue = (array) $answerValue;
                        if (empty($answerValue)) {
                            Log::warning('Réponse de correspondance vide après cast', [
                                'question_id' => $questionId,
                                'answer_value' => $answerValue
                            ]);
                            continue;
                        }

                        $answerPairs = [];
                        foreach ($answerValue as $leftId => $rightText) {
                            if ($rightText === null || $rightText === '') {
                                continue;
                            }

                            $leftAnswer = $question->reponses->firstWhere('id', $leftId);
                            $leftText = $leftAnswer ? $leftAnswer->text : 'unknown';
                            $answerPairs[] = ['left' => $leftText, 'right' => $rightText];
                        }

                        QuizParticipationAnswer::create([
                            'participation_id' => $participation->id,
                            'question_id' => $questionId,
                            'answer_ids' => array_keys($answerValue),
                            'answer_texts' => json_encode($answerPairs)
                        ]);
                    } else {
                        $answerToStore = is_array($answerValue) || is_object($answerValue)
                            ? json_encode((array) $answerValue)
                            : $answerValue;

                        QuizParticipationAnswer::create([
                            'participation_id' => $participation->id,
                            'question_id' => $questionId,
                            'answer_texts' => $answerToStore
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Erreur lors du traitement de la réponse', [
                        'question_id' => $questionId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'answer_value' => $answerValue
                    ]);
                    continue;
                }
            }

            // Marquer la participation comme terminée
            $participation->update([
                'status' => 'completed',
                'score' => $score,
                'correct_answers' => $correctAnswers,
                'time_spent' => $request->timeSpent,
                'completed_at' => now(),
            ]);

            // Mettre à jour le classement
            $this->updateClassement($quiz->id, $stagiaire->id, $score);

            // Vérifier les achievements après soumission du quiz
            $achievementService = app(\App\Services\StagiaireAchievementService::class);
            $newAchievements = $achievementService->checkAchievements($stagiaire, $quiz->id);

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
                'questions' => $questionsDetails->values()->all(),
                'newAchievements' => $newAchievements
            ])->setStatusCode(201, 'Résultat du quiz soumis avec succès');
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



    private function updateClassement($quizId, $stagiaireId, $points)
    {
        try {
            $classement = Classement::where('quiz_id', $quizId)
                ->where('stagiaire_id', $stagiaireId)
                ->first();

            if ($classement) {
                if ($points > $classement->points) {
                    $classement->update(['points' => $points]);
                }
            } else {
                $classement = Classement::create([
                    'quiz_id' => $quizId,
                    'stagiaire_id' => $stagiaireId,
                    'points' => $points
                ]);
            }

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
            $stagiaire = Stagiaire::where('user_id', $user->getKey())->first();

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
                ->map(function ($quiz) {
                    return [
                        'id' => (string) $quiz->id,
                        'titre' => $quiz->titre,
                        'description' => $quiz->description,
                        'categorie' => $quiz->formation->categorie ?? 'Non catégorisé',
                        'categorieId' => $quiz->formation->categorie ?? 'non-categorise',
                        'niveau' => $quiz->niveau ?? 'débutant',
                        'questions' => $quiz->questions->map(function ($question) {
                            return [
                                'id' => (string) $question->id,
                                'text' => $question->text,
                                'type' => $question->type ?? 'multiplechoice',
                                'answers' => $question->reponses->map(function ($reponse) {
                                    return [
                                        'id' => (string) $reponse->id,
                                        'text' => $reponse->text,
                                        'isCorrect' => (bool) $reponse->is_correct
                                    ];
                                })->toArray()
                            ];
                        })->toArray(),
                        'points' => (int) ($quiz->nb_points_total ?? 0)
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
                ->map(function ($item) {
                    return [
                        'id' => (string) $item->id,
                        'rang' => $item->rang,
                        'points' => $item->points,
                        'stagiaire' => [
                            'id' => (string) $item->stagiaire->id,
                            'prenom' => $item->stagiaire->prenom,
                            'image' => $item->stagiaire->user->image ?? null
                        ],
                        'quiz' => [
                            'id' => (string) $item->quiz->id,
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
            $period = request('period', 'all');
            $quarter = (int) request('quarter', 0);
            $month = request('month') ? (int) request('month') : null;
            $year = request('year') ? (int) request('year') : now()->year;

            Log::info('Global ranking requested', [
                'period' => $period,
                'quarter' => $quarter,
                'month' => $month,
                'year' => $year
            ]);

            // Charger les classements avec stagiaire + user + formateurs + leurs formations
            $query = Classement::with([
                'stagiaire.user',
                'stagiaire.formateurs.user',
                'stagiaire.catalogue_formations.formation',
                'quiz'
            ]);

            // Appliquer le filtre de période si nécessaire
            if ($period === 'week') {
                $query->where('updated_at', '>=', now()->startOfWeek());
            } elseif ($period === 'month') {
                $m = $month ?? now()->month;
                $query->whereMonth('updated_at', $m)->whereYear('updated_at', $year);
            } elseif ($period === 'all' && $quarter) {
                $start = ($quarter - 1) * 3 + 1;
                $end = $start + 2;
                $query->whereBetween(DB::raw('MONTH(updated_at)'), [$start, $end])->whereYear('updated_at', $year);
            }

            $raw = $query->get();

            if ($raw->isEmpty()) {
                // Conserver la même forme (tableau) — le client affichera 0 ou vide selon sa logique
                return response()->json([]);
            }

            $classements = $raw
                ->groupBy('stagiaire_id')
                ->map(function ($group) {
                    $totalPoints = $group->sum('points');
                    $quizCount = $group->count();
                    $averageScore = $quizCount > 0 ? round($totalPoints / $quizCount, 2) : 0;

                    $stagiaire = $group->first()->stagiaire;

                    return [
                        'stagiaire' => [
                            'id' => (string) $stagiaire->id,
                            'prenom' => $stagiaire->prenom,
                            'nom' => $stagiaire->user->name ?? '',
                            'image' => $stagiaire->user->image ?? null,
                        ],
                        'formateurs' => $stagiaire->formateurs->map(function ($formateur) use ($stagiaire) {
                            $formationsAssignees = $stagiaire->catalogue_formations
                                ->where('pivot.formateur_id', $formateur->id)
                                ->map(function ($catalogueFormation) {
                                    return [
                                        'id' => $catalogueFormation->id,
                                        'titre' => $catalogueFormation->titre,
                                        'description' => $catalogueFormation->description,
                                        'duree' => $catalogueFormation->duree,
                                        'tarif' => $catalogueFormation->tarif,
                                        'statut' => $catalogueFormation->statut,
                                        'image_url' => $catalogueFormation->image_url,
                                        'formation' => $catalogueFormation->formation ? [
                                            'id' => $catalogueFormation->formation->id,
                                            'titre' => $catalogueFormation->formation->titre,
                                            'categorie' => $catalogueFormation->formation->categorie,
                                            'icon' => $catalogueFormation->formation->icon,
                                        ] : null
                                    ];
                                });

                            return [
                                'id' => $formateur->id,
                                'civilite' => $formateur->civilite,
                                'prenom' => $formateur->prenom,
                                'nom' => $formateur->user->name,
                                'telephone' => $formateur->telephone,
                                'image' => $formateur->user->image ?? null,
                                'formations' => $formationsAssignees
                            ];
                        })->filter(function ($formateur) {
                            return $formateur['formations']->isNotEmpty();
                        })->values(),
                        'totalPoints' => $totalPoints,
                        'quizCount' => $quizCount,
                        'averageScore' => $averageScore,
                    ];
                })
                ->sortByDesc('totalPoints')
                ->values()
                ->map(function ($item, $index) {
                    return array_merge($item, ['rang' => $index + 1]);
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


    public function startParticipation($quizId, Request $request = null)
    {
        try {
            // Vérifier si le quiz existe
            $quiz = Quiz::find($quizId);
            if (!$quiz) {
                return response()->json(['message' => 'Quiz non trouvé'], 404);
            }

            // Vérifier si l'utilisateur a déjà une participation en cours
            $existingParticipation = QuizParticipation::where('user_id', auth()->user()->getKey())
                ->where('quiz_id', $quizId)
                ->where('status', 'in_progress')
                ->first();

            if ($existingParticipation) {
                return response()->json([
                    'message' => 'Participation en cours trouvée',
                    'participation' => $existingParticipation
                ], 200);
            }

            // Créer une nouvelle participation
            $participation = QuizParticipation::create([
                'user_id' => auth()->user()->getKey(),
                'quiz_id' => $quizId,
                'status' => 'in_progress',
                'started_at' => now(),
                'score' => 0,
                'correct_answers' => 0,
                'time_spent' => 0
            ]);

            return response()->json([
                'message' => 'Nouvelle participation créée',
                'participation' => $participation
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de la participation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCurrentParticipation($quizId)
    {
        try {
            $participation = QuizParticipation::where('user_id', auth()->user()->getKey())
                ->where('quiz_id', $quizId)
                ->where('status', 'in_progress')
                ->first();

            if (!$participation) {
                return response()->json([
                    'message' => 'Aucune participation en cours trouvée'
                ], 404);
            }

            return response()->json([
                'participation' => $participation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération de la participation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function completeParticipation(Request $request, $quizId)
    {
        try {
            $validated = $request->validate([
                'score' => 'required|integer|min:0',
                'correct_answers' => 'required|integer|min:0',
                'time_spent' => 'required|integer|min:0'
            ]);

            $participation = QuizParticipation::where('user_id', auth()->user()->getKey())
                ->where('quiz_id', $quizId)
                ->where('status', 'in_progress')
                ->firstOrFail();

            $participation->update([
                'status' => 'completed',
                'score' => $validated['score'],
                'correct_answers' => $validated['correct_answers'],
                'time_spent' => $validated['time_spent'],
                'completed_at' => now()
            ]);

            return response()->json([
                'message' => 'Participation terminée avec succès',
                'participation' => $participation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour de la participation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getGlobalRankings()
    {
        try {
            $rankings = QuizParticipation::where('status', 'completed')
                ->select('user_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('user_id')
                ->orderBy('total_score', 'desc')
                ->with('user:id,name')
                ->take(10)
                ->get();

            return response()->json([
                'rankings' => $rankings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des classements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getQuizRankings($quizId)
    {
        try {
            $rankings = QuizParticipation::where('quiz_id', $quizId)
                ->where('status', 'completed')
                ->select('user_id', 'score', 'correct_answers', 'time_spent')
                ->orderBy('score', 'desc')
                ->orderBy('time_spent', 'asc')
                ->with('user:id,name')
                ->take(10)
                ->get();

            return response()->json([
                'rankings' => $rankings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des classements du quiz',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API pour consulter le résumé d'une participation
    public function getParticipationResume($participationId)
    {
        $participation = QuizParticipation::with(['quiz.questions.reponses'])->findOrFail($participationId);
        $answers = QuizParticipationAnswer::where('participation_id', $participationId)->get()->keyBy('question_id');

        $resume = $participation->quiz->questions->map(function ($question) use ($answers) {
            $userAnswerIds = $answers[$question->id]->answer_ids ?? [];
            $correctAnswers = $question->reponses->where('is_correct', true)->pluck('id')->toArray();

            return [
                'question' => $question->text,
                'question_id' => $question->id,
                'correctAnswers' => $correctAnswers,
                'userAnswers' => $userAnswerIds,
                'answers' => $question->reponses->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'text' => $r->text,
                        'isCorrect' => $r->is_correct
                    ];
                })
            ];
        });

        return response()->json([
            'quiz' => [
                'id' => $participation->quiz->id,
                'titre' => $participation->quiz->titre,
            ],
            'questions' => $resume,
            'score' => $participation->score,
            'totalQuestions' => $participation->quiz->questions->count(),
        ]);
    }

    public function getUserParticipations($quizId)
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = Auth::user();

            // Vérifier si le quiz existe
            $quiz = Quiz::findOrFail($quizId);

            // Récupérer les participations de l'utilisateur pour ce quiz
            $participations = QuizParticipation::where('user_id', $user->getKey())
                ->where('quiz_id', $quizId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($participation) {
                    return [
                        'id' => $participation->id,
                        'score' => $participation->score,
                        'correct_answers' => $participation->correct_answers,
                        'time_spent' => $participation->time_spent,
                        'status' => $participation->status,
                        'completed_at' => $participation->completed_at ? $participation->completed_at->toISOString() : null,
                    ];
                });

            return response()->json([
                'quiz' => [
                    'id' => $quiz->id,
                    'title' => $quiz->titre,
                ],
                'participations' => $participations,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans getUserParticipations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des participations',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportQuiz($id)
    {
        try {
            $quiz = Quiz::with(['questions.reponses'])->findOrFail($id);

            $exportData = [
                'quiz' => [
                    'id' => $quiz->id,
                    'titre' => $quiz->titre,
                    'description' => $quiz->description,
                    'niveau' => $quiz->niveau,
                    'duree' => $quiz->duree,
                    'nb_points_total' => $quiz->nb_points_total,
                    'formation_id' => $quiz->formation_id,
                ],
                'questions' => $quiz->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'text' => $question->text,
                        'type' => $question->type,
                        'points' => $question->points,
                        'reponses' => $question->reponses->map(function ($reponse) {
                            return [
                                'id' => $reponse->id,
                                'text' => $reponse->text,
                                'is_correct' => $reponse->is_correct,
                                'position' => $reponse->position,
                                'match_pair' => $reponse->match_pair,
                                'bank_group' => $reponse->bank_group,
                                'flashcard_back' => $reponse->flashcard_back,
                            ];
                        }),
                    ];
                }),
            ];

            $fileName = 'quiz_export_' . $quiz->id . '.json';
            $filePath = storage_path('app/' . $fileName);
            file_put_contents($filePath, json_encode($exportData, JSON_PRETTY_PRINT));

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'exportation du quiz : ' . $e->getMessage());
        }
    }

    public function exportMultipleQuizzes(Request $request)
    {
        try {
            $quizIds = $request->input('quiz_ids', []);

            if (empty($quizIds)) {
                return redirect()->back()->with('error', 'Aucun quiz sélectionné pour l\'exportation.');
            }

            $quizzes = Quiz::with(['questions.reponses'])->whereIn('id', $quizIds)->get();

            $exportData = $quizzes->map(function ($quiz) {
                return [
                    'quiz' => [
                        'id' => $quiz->id,
                        'titre' => $quiz->titre,
                        'description' => $quiz->description,
                        'niveau' => $quiz->niveau,
                        'duree' => $quiz->duree,
                        'nb_points_total' => $quiz->nb_points_total,
                        'formation_id' => $quiz->formation_id,
                    ],
                    'questions' => $quiz->questions->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'text' => $question->text,
                            'type' => $question->type,
                            'points' => $question->points,
                            'reponses' => $question->reponses->map(function ($reponse) {
                                return [
                                    'id' => $reponse->id,
                                    'text' => $reponse->text,
                                    'is_correct' => $reponse->is_correct,
                                    'position' => $reponse->position,
                                    'match_pair' => $reponse->match_pair,
                                    'bank_group' => $reponse->bank_group,
                                    'flashcard_back' => $reponse->flashcard_back,
                                ];
                            }),
                        ];
                    }),
                ];
            });

            $fileName = 'quizzes_export_' . now()->format('Ymd_His') . '.json';
            $filePath = storage_path('app/' . $fileName);
            file_put_contents($filePath, json_encode($exportData, JSON_PRETTY_PRINT));

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'exportation des quiz : ' . $e->getMessage());
        }
    }

    public function getGlobalCategoryStats()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'error' => 'Utilisateur non authentifié'
                ], 401);
            }

            $categories = Formation::select('categorie as name')
                ->distinct()
                ->get();
            $stats = [];


            foreach ($categories as $category) {
                $quizCount = Quiz::whereHas('formation', function ($query) use ($category) {
                    $query->where('categorie', $category->name);
                })->count();

                $completedQuizzes = Quiz::whereHas('formation', function ($query) use ($category) {
                    $query->where('categorie', $category->name);
                })
                    ->whereHas('quiz_participations', function ($query) use ($user) {
                        $query->where('user_id', $user->getKey())
                            ->where('status', 'completed');
                    })
                    ->count();

                $stats[] = [
                    'category' => $category->name,
                    'totalQuizzes' => $quizCount,
                    'completedQuizzes' => $completedQuizzes,
                    'completionRate' => $quizCount > 0 ? round(($completedQuizzes / $quizCount) * 100, 2) : 0
                ];
            }

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Erreur dans getGlobalCategoryStats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des statistiques par catégorie',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getProgressStats()
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->stagiaire) {
                return response()->json([
                    'daily_progress' => [],
                    'weekly_progress' => [],
                    'monthly_progress' => [],
                ]);
            }

            $now = now();
            $thirtyDaysAgo = $now->copy()->subDays(30);

            // Progression quotidienne
            $dailyProgress = Classement::where('stagiaire_id', $user->stagiaire->id)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->get()
                ->groupBy(function ($classement) {
                    return $classement->created_at->format('Y-m-d');
                })
                ->map(function ($classements) {
                    return [
                        'date' => $classements->first()->created_at->format('Y-m-d'),
                        'completed_quizzes' => $classements->count(),
                        'average_points' => round($classements->avg('points'), 2),
                    ];
                })
                ->values();

            // Progression hebdomadaire
            $weeklyProgress = Classement::where('stagiaire_id', $user->stagiaire->id)
                ->where('created_at', '>=', $now->copy()->subWeeks(4))
                ->get()
                ->groupBy(function ($classement) {
                    return $classement->created_at->format('Y-W');
                })
                ->map(function ($classements) {
                    return [
                        'week' => $classements->first()->created_at->format('Y-W'),
                        'completed_quizzes' => $classements->count(),
                        'average_points' => round($classements->avg('points'), 2),
                    ];
                })
                ->values();

            // Progression mensuelle
            $monthlyProgress = Classement::where('stagiaire_id', $user->stagiaire->id)
                ->where('created_at', '>=', $now->copy()->subMonths(6))
                ->get()
                ->groupBy(function ($classement) {
                    return $classement->created_at->format('Y-m');
                })
                ->map(function ($classements) {
                    return [
                        'month' => $classements->first()->created_at->format('Y-m'),
                        'completed_quizzes' => $classements->count(),
                        'average_points' => round($classements->avg('points'), 2),
                    ];
                })
                ->values();

            return response()->json([
                'daily_progress' => $dailyProgress,
                'weekly_progress' => $weeklyProgress,
                'monthly_progress' => $monthlyProgress,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans getProgressStats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des statistiques de progression'], 500);
        }
    }
    public function getQuizTrends()
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->stagiaire) {
                return response()->json([
                    'category_trends' => [],
                    'overall_trend' => [],
                ]);
            }

            $categories = Formation::select('categorie as name')
                ->distinct()
                ->get();
            $thirtyDaysAgo = now()->subDays(30);

            // Tendances par catégorie
            $categoryTrends = $categories->map(function ($category) use ($user, $thirtyDaysAgo) {
                $trendData = QuizParticipation::where('user_id', $user->getKey())
                    ->whereHas('quiz.formation', function ($query) use ($category) {
                        $query->where('categorie', $category->name);
                    })
                    ->where('status', 'completed')
                    ->where('created_at', '>=', $thirtyDaysAgo)
                    ->get()
                    ->groupBy(function ($participation) {
                        return $participation->created_at->format('Y-m-d');
                    })
                    ->map(function ($participations) {
                        return [
                            'date' => $participations->first()->created_at->format('Y-m-d'),
                            'score' => round($participations->avg('score'), 2),
                        ];
                    })
                    ->values();

                return [
                    'category_id' => $category->name,
                    'category_name' => $category->name,
                    'trend_data' => $trendData,
                ];
            });

            // Tendance globale
            $overallTrend = QuizParticipation::where('user_id', $user->getKey())
                ->where('status', 'completed')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->get()
                ->groupBy(function ($participation) {
                    return $participation->created_at->format('Y-m-d');
                })
                ->map(function ($participations) {
                    return [
                        'date' => $participations->first()->created_at->format('Y-m-d'),
                        'average_score' => round($participations->avg('score'), 2),
                    ];
                })
                ->values();

            return response()->json([
                'category_trends' => $categoryTrends,
                'overall_trend' => $overallTrend,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans getQuizTrends', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des tendances des quiz'], 500);
        }
    }

    public function getQuizzesGroupedByFormation()
    {
        try {
            $user = Auth::user();
            $stagiaire = Stagiaire::where('user_id', $user->getKey())->first();

            if (!$stagiaire) {
                return response()->json(['error' => 'Aucun stagiaire associé à cet utilisateur'], 404);
            }

            // Récupérer les catalogues liés au stagiaire
            $catalogues = CatalogueFormation::whereHas('stagiaires', function ($query) use ($stagiaire) {
                $query->where('stagiaires.id', $stagiaire->id);
            })->pluck('id');

            // Récupérer les formations liées à ces catalogues
            $formations = Formation::whereHas('catalogueFormation', function ($query) use ($catalogues) {
                $query->whereIn('catalogue_formations.id', $catalogues);
            })
                ->with([
                    'quizzes' => function ($query) {
                        $query->where('status', 'actif')->with(['questions.reponses', 'formation']);
                    }
                ])
                ->get();

            // Formatter
            $formattedFormations = $formations->map(function ($formation) {
                return [
                    'id' => (string) $formation->id,
                    'titre' => $formation->titre,
                    'description' => $formation->description,
                    'categorie' => $formation->categorie,
                    'quizzes' => $formation->quizzes->map(function ($quiz) {
                        return [
                            'id' => (string) $quiz->id,
                            'titre' => $quiz->titre,
                            'description' => $quiz->description,
                            'categorie' => $quiz->formation->categorie ?? 'Non catégorisé',
                            'categorieId' => $quiz->formation->categorie ?? 'non-categorise',
                            'niveau' => $quiz->niveau ?? 'débutant',
                            'questions' => $quiz->questions->map(function ($question) {
                                return [
                                    'id' => (string) $question->id,
                                    'text' => $question->text,
                                    'type' => $question->type ?? 'multiplechoice',
                                    'answers' => $question->reponses->map(function ($reponse) {
                                        return [
                                            'id' => (string) $reponse->id,
                                            'text' => $reponse->text,
                                            'isCorrect' => (bool) $reponse->is_correct
                                        ];
                                    })->toArray()
                                ];
                            })->toArray(),
                            'points' => (int) ($quiz->nb_points_total ?? 0)
                        ];
                    })->toArray()
                ];
            });

            return response()->json($formattedFormations);
        } catch (\Exception $e) {
            Log::error('Erreur dans getQuizzesGroupedByFormation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des quizzes par formation',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getPerformanceStats()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->stagiaire) {
                return response()->json([
                    'strengths' => [],
                    'weaknesses' => [],
                    'improvement_areas' => [],
                ]);
            }

            $categories = Formation::select('categorie as name')
                ->distinct()
                ->get();

            $stats = [];

            foreach ($categories as $category) {
                $scores = QuizParticipation::where('user_id', $user->getKey())
                    ->whereHas('quiz.formation', function ($query) use ($category) {
                        $query->where('categorie', $category->name);
                    })
                    ->where('status', 'completed')
                    ->pluck('score')
                    ->toArray();

                if (count($scores) > 0) {
                    $averageScore = array_sum($scores) / count($scores);
                    $stats[] = [
                        'category_id' => $category->name,
                        'category_name' => $category->name,
                        'score' => round($averageScore, 2),
                    ];
                }
            }

            // Trier les scores pour identifier les forces et faiblesses
            usort($stats, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            $strengths = array_slice($stats, 0, 3);
            $weaknesses = array_slice($stats, -3);
            $improvementAreas = array_slice($stats, 3, -3);

            return response()->json([
                'strengths' => $strengths,
                'weaknesses' => $weaknesses,
                'improvement_areas' => $improvementAreas,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans getPerformanceStats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des statistiques de performance',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function resumeParticipation($quizId)
    {
        $user = Auth::user();

        $participation = QuizParticipation::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->first();

        if (!$participation) {
            return response()->json(['error' => 'No participation found'], 404);
        }

        $participation->load('answers');
        return response()->json($participation->resume_data);
    }
    public function saveProgress(Request $request, $quizId)
    {
        $validated = $request->validate([
            'current_question_id' => 'nullable|exists:questions,id',
            'answers' => 'nullable|array',
            'time_spent' => 'nullable|integer',
        ]);

        $user = Auth::user();

        $participation = QuizParticipation::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->firstOrFail();

        if ($request->has('current_question_id')) {
            $participation->current_question_id = $request->current_question_id;
        }

        if ($request->has('time_spent')) {
            $participation->time_spent = $request->time_spent;
        }

        $participation->save();

        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $answerData) {
                QuizParticipationAnswer::updateOrCreate(
                    [
                        'participation_id' => $participation->id,
                        'question_id' => $questionId
                    ],
                    [
                        'answer_ids' => $answerData
                    ]
                );
            }
        }

        return response()->json(['success' => true]);
    }
}
