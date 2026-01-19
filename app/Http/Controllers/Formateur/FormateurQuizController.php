<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Questions;
use App\Models\Reponse;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormateurQuizController extends Controller
{
    /**
     * Vérifier que l'utilisateur est un formateur
     */
    private function checkFormateur()
    {
        $user = Auth::user();
        if ($user->role !== 'formateur' && $user->role !== 'formatrice') {
            abort(403, 'Accès réservé aux formateurs.');
        }

        if (!$user->formateur) {
            abort(404, 'Profil formateur non trouvé.');
        }
    }

    /**
     * API: Get all quizzes (formateur can manage all or filter by formation)
     * GET /formateur/quizzes
     */
    public function index(Request $request)
    {
        $this->checkFormateur();

        $query = Quiz::with(['questions', 'formation']);

        // Filter by formation if provided
        if ($request->has('formation_id')) {
            $query->where('formation_id', $request->formation_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $quizzes = $query->orderBy('created_at', 'desc')->get();

        $quizzesData = $quizzes->map(function ($quiz) {
            return [
                'id' => $quiz->id,
                'titre' => $quiz->titre,
                'description' => $quiz->description,
                'duree' => $quiz->duree,
                'niveau' => $quiz->niveau,
                'nb_points_total' => $quiz->nb_points_total,
                'status' => $quiz->status ?? 'actif',
                'formation' => $quiz->formation ? [
                    'id' => $quiz->formation->id,
                    'nom' => $quiz->formation->nom,
                ] : null,
                'nb_questions' => $quiz->questions->count(),
                'created_at' => $quiz->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'quizzes' => $quizzesData,
        ]);
    }

    /**
     * API: Get a single quiz with questions and answers
     * GET /formateur/quizzes/{id}
     */
    public function show($id)
    {
        $this->checkFormateur();

        $quiz = Quiz::with(['questions.reponses', 'formation'])->findOrFail($id);

        $questionsData = $quiz->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question' => $question->text, // text field in DB
                'type' => $question->type ?? 'qcm',
                'ordre' => $question->ordre ?? 0,
                'reponses' => $question->reponses->map(function ($reponse) {
                    return [
                        'id' => $reponse->id,
                        'reponse' => $reponse->text, // text field in DB
                        'correct' => (bool) $reponse->is_correct, // is_correct field in DB
                    ];
                }),
            ];
        })->sortBy('ordre')->values();

        return response()->json([
            'quiz' => [
                'id' => $quiz->id,
                'titre' => $quiz->titre,
                'description' => $quiz->description,
                'duree' => $quiz->duree,
                'niveau' => $quiz->niveau,
                'nb_points_total' => $quiz->nb_points_total,
                'status' => $quiz->status ?? 'actif',
                'formation_id' => $quiz->formation_id,
                'formation' => $quiz->formation ? [
                    'id' => $quiz->formation->id,
                    'nom' => $quiz->formation->nom,
                ] : null,
            ],
            'questions' => $questionsData,
        ]);
    }

    /**
     * API: Create a new quiz
     * POST /formateur/quizzes
     */
    public function store(Request $request)
    {
        $this->checkFormateur();

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree' => 'required|integer|min:1',
            'niveau' => 'required|string|in:debutant,intermediaire,avance',
            'formation_id' => 'nullable|exists:formations,id',
            'status' => 'nullable|string|in:brouillon,actif,archive',
        ]);

        $quiz = Quiz::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'duree' => $request->duree,
            'niveau' => $request->niveau,
            'formation_id' => $request->formation_id,
            'status' => $request->status ?? 'brouillon',
            'nb_points_total' => 0, // Will be calculated when questions are added
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quiz créé avec succès',
            'quiz' => [
                'id' => $quiz->id,
                'titre' => $quiz->titre,
                'status' => $quiz->status,
            ],
        ], 201);
    }

    /**
     * API: Update a quiz
     * PUT /formateur/quizzes/{id}
     */
    public function update(Request $request, $id)
    {
        $this->checkFormateur();

        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'duree' => 'sometimes|required|integer|min:1',
            'niveau' => 'sometimes|required|string|in:debutant,intermediaire,avance',
            'formation_id' => 'nullable|exists:formations,id',
            'status' => 'nullable|string|in:brouillon,actif,archive',
        ]);

        $quiz->update($request->only([
            'titre',
            'description',
            'duree',
            'niveau',
            'formation_id',
            'status',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Quiz mis à jour',
            'quiz' => [
                'id' => $quiz->id,
                'titre' => $quiz->titre,
            ],
        ]);
    }

    /**
     * API: Delete a quiz
     * DELETE /formateur/quizzes/{id}
     */
    public function destroy($id)
    {
        $this->checkFormateur();

        $quiz = Quiz::findOrFail($id);

        // Check if quiz has participations
        if ($quiz->quiz_participations()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer un quiz avec des participations. Archivez-le plutôt.',
            ], 400);
        }

        $quiz->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quiz supprimé',
        ]);
    }

    /**
     * API: Add a question to a quiz
     * POST /formateur/quizzes/{id}/questions
     */
    public function addQuestion(Request $request, $id)
    {
        $this->checkFormateur();

        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'question' => 'required|string',
            'type' => 'nullable|string|in:qcm,vrai_faux,text',
            'ordre' => 'nullable|integer',
            'reponses' => 'required|array|min:2',
            'reponses.*.reponse' => 'required|string',
            'reponses.*.correct' => 'required|boolean',
        ]);

        // Check at least one correct answer
        $hasCorrect = collect($request->reponses)->contains('correct', true);
        if (!$hasCorrect) {
            return response()->json([
                'success' => false,
                'message' => 'Au moins une réponse doit être correcte',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $question = Questions::create([
                'quiz_id' => $quiz->id,
                'text' => $request->question,
                'type' => $request->type ?? 'qcm',
                'ordre' => $request->ordre ?? ($quiz->questions()->count() + 1),
            ]);

            foreach ($request->reponses as $reponseData) {
                Reponse::create([
                    'question_id' => $question->id,
                    'text' => $reponseData['reponse'],
                    'is_correct' => $reponseData['correct'] ? 1 : 0,
                ]);
            }

            // Update quiz points total
            $quiz->nb_points_total = $quiz->questions()->count() * 2;
            $quiz->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Question ajoutée',
                'question' => [
                    'id' => $question->id,
                    'question' => $question->text,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de la question',
            ], 500);
        }
    }

    /**
     * API: Update a question
     * PUT /formateur/quizzes/{quizId}/questions/{questionId}
     */
    public function updateQuestion(Request $request, $quizId, $questionId)
    {
        $this->checkFormateur();

        $quiz = Quiz::findOrFail($quizId);
        $question = Questions::where('quiz_id', $quiz->id)->findOrFail($questionId);

        $request->validate([
            'question' => 'sometimes|required|string',
            'type' => 'nullable|string|in:qcm,vrai_faux,text',
            'ordre' => 'nullable|integer',
            'reponses' => 'sometimes|required|array|min:2',
            'reponses.*.reponse' => 'required|string',
            'reponses.*.correct' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            if ($request->has('question')) {
                $question->text = $request->question;
            }
            $question->update($request->only(['type', 'ordre']));

            if ($request->has('reponses')) {
                // Delete old answers
                Reponse::where('question_id', $question->id)->delete();

                // Add new answers
                foreach ($request->reponses as $reponseData) {
                    Reponse::create([
                        'question_id' => $question->id,
                        'text' => $reponseData['reponse'],
                        'is_correct' => $reponseData['correct'] ? 1 : 0,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Question mise à jour',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
            ], 500);
        }
    }

    /**
     * API: Delete a question
     * DELETE /formateur/quizzes/{quizId}/questions/{questionId}
     */
    public function deleteQuestion($quizId, $questionId)
    {
        $this->checkFormateur();

        $quiz = Quiz::findOrFail($quizId);
        $question = Questions::where('quiz_id', $quiz->id)->findOrFail($questionId);

        $question->delete();

        // Update quiz points
        $quiz->nb_points_total = $quiz->questions()->count() * 2;
        $quiz->save();

        return response()->json([
            'success' => true,
            'message' => 'Question supprimée',
        ]);
    }

    /**
     * API: Publish a quiz (change status to actif)
     * POST /formateur/quizzes/{id}/publish
     */
    public function publish($id)
    {
        $this->checkFormateur();

        $quiz = Quiz::with('questions')->findOrFail($id);

        if ($quiz->questions->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de publier un quiz sans questions',
            ], 400);
        }

        $quiz->status = 'actif';
        $quiz->save();

        return response()->json([
            'success' => true,
            'message' => 'Quiz publié avec succès',
        ]);
    }

    /**
     * API: Get available formations for quiz assignment
     * GET /formateur/formations-list
     */
    public function getFormations()
    {
        $this->checkFormateur();

        $formations = Formation::select('id', 'nom')->orderBy('nom')->get();

        return response()->json([
            'formations' => $formations,
        ]);
    }
}
