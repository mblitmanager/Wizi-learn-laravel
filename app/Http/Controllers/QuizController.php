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
                ->where('status', 'actif')
                ->whereHas('formation', function ($query) use ($category) {
                    $query->where('categorie', $category)
                        ->whereHas('stagiaires', function ($query) {
                            $query->where('role', 'stagiaire');
                        });
                })
                ->get();

            // Transformer les données pour correspondre au format TypeScript
            $formattedQuizzes = $quizzes->map(function ($quiz) {
                return [
                    'id' => (string)$quiz->id,
                    'titre' => $quiz->titre,
                    'description' => $quiz->description,
                    'categorie' => $quiz->formation->categorie ?? 'Non catégorisé',
                    'categorieId' => $quiz->formation->categorie ?? 'non-categorise',
                    'niveau' => $quiz->niveau ?? 'débutant',
                    'questions' => $quiz->questions->map(function ($question) {
                        $questionData = [
                            'id' => (string)$question->id,
                            'text' => $question->text,
                            'type' => $question->type ?? 'choix multiples',
                        ];

                        // Gestion spécifique selon le type de question
                        switch ($question->type) {
                            case 'choix multiples':
                            case 'vrai/faux':
                                $questionData['answers'] = $question->reponses->map(function ($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'isCorrect' => (bool)$reponse->is_correct
                                    ];
                                })->toArray();
                                break;

                            case 'rearrangement':
                                $questionData['answers'] = $question->reponses->map(function ($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'position' => (int)$reponse->position
                                    ];
                                })->sortBy('position')->values()->toArray();
                                break;

                            case 'remplir le champ vide':
                                $questionData['blanks'] = $question->reponses->map(function ($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'bankGroup' => $reponse->bank_group
                                    ];
                                })->toArray();
                                break;

                            case 'banque de mots':
                                $questionData['wordbank'] = $question->reponses->map(function ($reponse) {
                                    return [
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'isCorrect' => (bool)$reponse->is_correct,
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
                                        'id' => (string)$reponse->id,
                                        'text' => $reponse->text,
                                        'matchPair' => $reponse->match_pair
                                    ];
                                })->toArray();
                                break;

                            case 'question audio':
                                $questionData['audioUrl'] = $question->audio_url ?? $question->media_url ?? null;
                                $questionData['answers'] = $question->reponses->map(function ($reponse) {
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
                ->whereHas('formation.stagiaires', function ($query) use ($stagiaire) {
                    $query->where('stagiaires.id', $stagiaire->id);
                })
                ->select('formation_id')
                ->distinct()
                ->get()
                ->map(function ($quiz) use ($stagiaire) {
                    return [
                        'id' => $quiz->formation->categorie ?? 'non-categorise',
                        'name' => $quiz->formation->categorie ?? 'Non catégorisé',
                        'color' => $this->getCategoryColor($quiz->formation->categorie ?? 'Non catégorisé'),
                        'icon' => $this->getCategoryIcon($quiz->formation->categorie ?? 'Non catégorisé'),
                        'description' => $this->getCategoryDescription($quiz->formation->categorie ?? 'Non catégorisé'),
                        'quizCount' => Quiz::where('formation_id', $quiz->formation_id)
                            ->whereHas('formation.stagiaires', function ($query) use ($stagiaire) {
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

        $history = Progression::with(['quiz' => function ($query) {
            $query->with(['questions.reponses']);
        }])
            ->where('stagiaire_id', $stagiaire->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($progression) {
                $niveau = $progression->quiz->niveau ?? 'débutant';
                $totalQuestions = $progression->total_questions;
                if ($niveau === 'débutant' && $totalQuestions > 5) {
                    $totalQuestions = 5;
                } elseif ($niveau === 'intermédiaire' && $totalQuestions > 10) {
                    $totalQuestions = 10;
                } elseif ($niveau === 'avancé' && $totalQuestions > 20) {
                    $totalQuestions = 20;
                }
                return [
                    'id' => (string)$progression->id,
                    'quiz' => [
                        'id' => (string)$progression->quiz->id,
                        'title' => $progression->quiz->titre,
                        'category' => $progression->quiz->formation->categorie ?? 'Non catégorisé',
                        'level' => $niveau
                    ],
                    'score' => $progression->score,
                    'completedAt' => $progression->created_at->toISOString(),
                    'timeSpent' => $progression->time_spent,
                    'totalQuestions' => $totalQuestions,
                    'correctAnswers' => $progression->correct_answers
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
        $categoryStats = $progressions->groupBy(function ($progression) {
            return $progression->quiz->formation->categorie ?? 'Non catégorisé';
        })->map(function ($group) {
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
            $levelQuizzes = $progressions->filter(function ($progression) use ($level) {
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
                'questions' => $quiz->questions->map(function ($question) {
                    $questionData = [
                        'id' => (string)$question->id,
                        'text' => $question->text,
                        'type' => $question->type ?? 'choix multiples',
                    ];

                    // Par défaut, toutes les questions ont des réponses
                    $questionData['answers'] = $question->reponses->map(function ($reponse) {
                        return [
                            'id' => (string)$reponse->id,
                            'text' => $reponse->text,
                            'isCorrect' => (bool)$reponse->is_correct
                        ];
                    })->toArray();

                    // Ajout des propriétés spécifiques selon le type
                    switch ($question->type) {
                        case 'rearrangement':
                            $questionData['answers'] = $question->reponses->map(function ($reponse) {
                                return [
                                    'id' => (string)$reponse->id,
                                    'text' => $reponse->text,
                                    'position' => (int)$reponse->position
                                ];
                            })->sortBy('position')->values()->toArray();
                            break;

                        case 'remplir le champ vide':
                            $questionData['blanks'] = $question->reponses->map(function ($reponse) {
                                return [
                                    'id' => (string)$reponse->id,
                                    'text' => $reponse->text,
                                    'bankGroup' => $reponse->bank_group
                                ];
                            })->toArray();
                            break;

                        case 'banque de mots':
                            $questionData['wordbank'] = $question->reponses->map(function ($reponse) {
                                return [
                                    'id' => (string)$reponse->id,
                                    'text' => $reponse->text,
                                    'isCorrect' => (bool)$reponse->is_correct,
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
                                    'id' => (string)$reponse->id,
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

    private function isAnswerCorrect($question, $selectedAnswers)
    {
        if ($question->type === 'correspondance') {
            // Récupérer uniquement les paires pays → capitale
            $correctPairs = CorrespondancePair::where('question_id', $question->id)
                ->whereIn('left_text', $question->reponses->pluck('text')->toArray())
                ->get()
                ->mapWithKeys(fn($pair) => [$pair->left_text => $pair->right_text])
                ->toArray();

            // Créer le map des réponses
            $reponsesMap = $question->reponses->pluck('text', 'id')->toArray();

            // Convertir les `selectedAnswers` en utilisant les `left_id` pour obtenir le `left_text`
            $selectedPairs = [];

            foreach ($selectedAnswers as $leftId => $rightText) {
                if (isset($reponsesMap[$leftId])) {
                    $leftText = $reponsesMap[$leftId];
                    $selectedPairs[$leftText] = $rightText;
                }
            }

            // Créer le tableau `match_pair`
            $matchPairs = CorrespondancePair::where('question_id', $question->id)
                ->whereIn('left_text', $question->reponses->pluck('text')->toArray())
                ->get()
                ->map(function ($pair) {
                    return [
                        'left' => $pair->left_text,
                        'right' => $pair->right_text
                    ];
                });

            $isCorrect = $selectedPairs == $correctPairs;

            return [
                'selectedAnswers' => $selectedPairs,
                'correctAnswers' => $correctPairs,
                'isCorrect' => $isCorrect,
                'match_pair' => $matchPairs
            ];
        }

        // Traitement pour "question audio"
        if ($question->type === 'question audio') {
            $correctAnswers = $question->reponses
                ->where('is_correct', true)
                ->pluck('text')
                ->toArray();

            // Gestion du format {"id": "3", "text": "Arctique"}
            $selectedText = is_array($selectedAnswers)
                ? ($selectedAnswers['text'] ?? null)
                : (is_object($selectedAnswers) ? $selectedAnswers->text : null);

            return [
                'selectedAnswers' => $selectedText,
                'correctAnswers' => $correctAnswers,
                'isCorrect' => in_array($selectedText, $correctAnswers)
            ];
        }

        // Traitement pour "remplir le champ vide"
        if ($question->type === 'remplir le champ vide') {
            // Préparer la map bank_group => bonne réponse
            $correctBlanks = [];
            foreach ($question->reponses as $reponse) {
                if ($reponse->bank_group && $reponse->is_correct) {
                    $correctBlanks[$reponse->bank_group] = $reponse->text;
                }
            }

            $normalize = function ($v) {
                return is_string($v) ? mb_strtolower(trim($v)) : $v;
            };

            $isCorrect = false;
            $details = [
                'selectedAnswers' => $selectedAnswers,
                'correctAnswers' => $correctBlanks,
                'isCorrect' => false
            ];

            if (is_array($selectedAnswers) && count($correctBlanks) > 0 && count($selectedAnswers) > 0 && array_keys($selectedAnswers) !== range(0, count($selectedAnswers) - 1)) {
                // Cas objet: mapping blank ids (bank_group) => réponse
                $allCorrect = true;
                // Ne comparer que les clés attendues (blanks de la question)
                foreach ($correctBlanks as $blankId => $correctText) {
                    $userText = $selectedAnswers[$blankId] ?? null;
                    if ($normalize($userText) !== $normalize($correctText)) {
                        $allCorrect = false;
                        break;
                    }
                }
                // Vérifier qu'il n'y a pas de réponses en trop pour cette question
                $userBlanks = array_filter(array_keys($selectedAnswers), function ($k) use ($correctBlanks) {
                    return array_key_exists($k, $correctBlanks);
                });
                if ($allCorrect && count($userBlanks) === count($correctBlanks)) {
                    $isCorrect = true;
                }
                $details['isCorrect'] = $isCorrect;
                return $details;
            } else if (is_array($selectedAnswers)) {
                // Fallback: comparer par ordre (array simple)
                $correctAnswers = array_values(array_filter($question->reponses->toArray(), function ($r) {
                    return $r['is_correct'];
                }));
                $correctTexts = array_map(function ($r) use ($normalize) {
                    return $normalize($r['text']);
                }, $correctAnswers);
                $userTexts = array_map($normalize, array_values($selectedAnswers));
                $isCorrect = $userTexts === $correctTexts;
                $details['correctAnswers'] = $correctTexts;
                $details['isCorrect'] = $isCorrect;
                return $details;
            }
            // Si aucun format reconnu, faux par défaut
            return $details;
        }

        // Traitement pour "choix multiples"
        if ($question->type === 'choix multiples') {
            $correctAnswers = $question->reponses
                ->where('is_correct', true)
                ->pluck('text')
                ->toArray();

            $selectedAnswers = is_array($selectedAnswers) ? $selectedAnswers : [];

            return [
                'selectedAnswers' => $selectedAnswers,
                'correctAnswers' => $correctAnswers,
                'isCorrect' => empty(array_diff($selectedAnswers, $correctAnswers)) &&
                    empty(array_diff($correctAnswers, $selectedAnswers))
            ];
        }
         // Traitement pour "banque de mots"
        if ($question->type === 'banque de mots') {
            $correctAnswers = $question->reponses
                ->where('is_correct', true)
                ->pluck('text')
                ->toArray();

            $selectedAnswers = is_array($selectedAnswers) ? $selectedAnswers : [];

            return [
                'selectedAnswers' => $selectedAnswers,
                'correctAnswers' => $correctAnswers,
                'isCorrect' => empty(array_diff($selectedAnswers, $correctAnswers)) &&
                    empty(array_diff($correctAnswers, $selectedAnswers))
            ];
        }
         // Traitement pour "vrai/faux"
        if ($question->type === 'vrai/faux') {
            $correctAnswers = $question->reponses
                ->where('is_correct', true)
                ->pluck('text')
                ->toArray();

            $selectedAnswers = is_array($selectedAnswers) ? $selectedAnswers : [];

            return [
                'selectedAnswers' => $selectedAnswers,
                'correctAnswers' => $correctAnswers,
                'isCorrect' => empty(array_diff($selectedAnswers, $correctAnswers)) &&
                    empty(array_diff($correctAnswers, $selectedAnswers))
            ];
        }

        if ($question->type === 'rearrangement') {
            // On suppose que selectedAnswers est un tableau d'IDs de réponses dans l'ordre donné
            $correctAnswers = $question->reponses
                ->where('is_correct', true)
                ->sortBy('position')
                ->pluck('text')
                ->values()
                ->toArray();

            $selectedAnswers = is_array($selectedAnswers) ? array_values($selectedAnswers) : [];

            $isCorrect = $selectedAnswers === $correctAnswers;

            return [
                'selectedAnswers' => $selectedAnswers,
                'correctAnswers' => $correctAnswers,
                'isCorrect' => $isCorrect
            ];
        }

        // Comparaison standard
        $correctAnswers = $question->reponses
            ->where('is_correct', true)
            ->pluck('text')
            ->toArray();
        return empty(array_diff($selectedAnswers, $correctAnswers)) &&
            empty(array_diff($correctAnswers, $selectedAnswers));
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

            // Récupérer la participation en cours pour l'utilisateur et le quiz
            $participation = QuizParticipation::where('user_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->where('status', 'in_progress')
                ->first();

            if (!$participation) {
                // Si aucune participation en cours, on peut en créer une ou retourner une erreur
                $participation = QuizParticipation::create([
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id,
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'score' => 0,
                    'correct_answers' => 0,
                    'time_spent' => 0
                ]);
            }


            // Préparer les détails des questions et réponses
            $questionsDetails = $quiz->questions->map(function ($question) use ($request) {
                $selectedAnswers = is_array($request->answers[$question->id] ?? null)
                    ? array_map(fn($answer) => is_array($answer) ? $answer['text'] : $answer, $request->answers[$question->id])
                    : [];


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
            $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions)*2) : 0;

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

            // Enregistrer les réponses pour les questions de type "correspondance"
            foreach ($request->answers as $questionId => $answerValue) {
                Log::info('Traitement de la question :', [
                    'question_id' => $questionId,
                    'answers' => $answerValue
                ]);

                $question = $quiz->questions->firstWhere('id', $questionId);

                if (!$question) continue;

                if ($question->type === 'correspondance') {
                    // Format: [left_id => right_text]
                    $answerPairs = [];
                    $newPairs = [];

                    foreach ($answerValue as $leftId => $rightText) {
                        // $leftAnswer = $question->reponses->firstWhere('id', $leftId);

                        // $leftText = $leftAnswer ? $leftAnswer->text : 'unknown';
                        // $answerPairs[] = ['left' => $leftText, 'right' => $rightText];

                        // Vérifie si la paire existe déjà
                        // $exists = CorrespondancePair::where('question_id', $questionId)
                        //     ->where('left_text', $leftText)
                        //     ->where('right_text', $rightText)
                        //     ->exists();

                        // if (!$exists) {
                        //     $newPairs[] = [
                        //         'question_id' => $questionId,
                        //         'left_text' => $leftText,
                        //         'right_text' => $rightText
                        //     ];
                        // }
                    }

                    Log::info('Paires pour la question :', [
                        'question_id' => $questionId,
                        'answerPairs' => $answerPairs,
                        'newPairs' => $newPairs
                    ]);

                    // if (!empty($newPairs)) {
                    //     CorrespondancePair::insert($newPairs);
                    // }

                    QuizParticipationAnswer::create([
                        'participation_id' => $participation->id,
                        'question_id' => $questionId,
                        'answer_ids' => array_keys($answerValue),
                        'answer_texts' => json_encode($answerPairs),
                    ]);
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
            $this->updateClassement($quiz->id, $stagiaire->id, $correctAnswers);
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
                ->map(function ($quiz) {
                    return [
                        'id' => (string)$quiz->id,
                        'titre' => $quiz->titre,
                        'description' => $quiz->description,
                        'categorie' => $quiz->formation->categorie ?? 'Non catégorisé',
                        'categorieId' => $quiz->formation->categorie ?? 'non-categorise',
                        'niveau' => $quiz->niveau ?? 'débutant',
                        'questions' => $quiz->questions->map(function ($question) {
                            return [
                                'id' => (string)$question->id,
                                'text' => $question->text,
                                'type' => $question->type ?? 'multiplechoice',
                                'answers' => $question->reponses->map(function ($reponse) {
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
                ->map(function ($item) {
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
                ->values()
                ->map(function ($item, $index) {
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

    public function startParticipation($quizId, Request $request = null)
    {
        try {
            // Vérifier si le quiz existe
            $quiz = Quiz::find($quizId);
            if (!$quiz) {
                return response()->json(['message' => 'Quiz non trouvé'], 404);
            }

            // Vérifier si l'utilisateur a déjà une participation en cours
            $existingParticipation = QuizParticipation::where('user_id', auth()->id())
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
                'user_id' => auth()->id(),
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
            $participation = QuizParticipation::where('user_id', auth()->id())
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

            $participation = QuizParticipation::where('user_id', auth()->id())
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
            $participations = QuizParticipation::where('user_id', $user->id)
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
}
