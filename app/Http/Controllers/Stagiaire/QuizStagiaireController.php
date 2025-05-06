<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizParticipation;
use App\Models\QuizParticipationAnswer;
use App\Models\Stagiaire;
use App\Models\User;
use App\Services\QuizService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizStagiaireController extends Controller
{
    protected $quizService;


    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function getQuizzesByStagiaire($stagiaireId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            if ($user->id != $stagiaireId && $user->role != 'formateur' && $user->role != 'admin') {
                // Vérifier si l'utilisateur est associé à ce stagiaire
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire || $userStagiaire->id != $stagiaireId) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }

            $quizzes = $this->quizService->getQuizzesByStagiaire($stagiaireId);
            return response()->json($quizzes);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        }
    }

    public function getCategories()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $categories = $this->quizService->getCategories();
            return response()->json($categories);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        }
    }
    public function getQuestionsByQuizId($quizId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $questions = $this->quizService->getQuestionsByQuizId($quizId);
            return response()->json([
                'data' => $questions
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function submitQuiz(Request $request, $quizId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stagiaireId = $user->id;

            // Transformer les données reçues dans le format attendu
            $answers = [];
            foreach ($request->all() as $answer) {
                $answers[$answer['questionId']] = $answer['reponseId'];
            }

            $result = $this->quizService->submitQuizAnswers($quizId, $stagiaireId, $answers);
            return response()->json($result);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getStagiaireQuizzes()
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

            // Récupérer les quiz du stagiaire avec leurs questions et réponses
            $quizzes = $this->quizService->getQuizzesByStagiaire($user->stagiaire->id);

            return response()->json([
                'data' => $quizzes
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
    }

    public function submitRearrangement(Request $request, Quiz $quiz)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'array'
        ]);

        $stagiaire = Stagiaire::find(18);

        $result = $this->submitRearrangementQuiz($request->input('answers'), $quiz, $stagiaire);

        return response()->json($result);
    }

    function submitRearrangementQuiz(array $userAnswers, Quiz $quiz, Stagiaire $stagiaire)
    {
        return DB::transaction(function () use ($userAnswers, $quiz, $stagiaire) {
            // Créer la participation
            $participation = QuizParticipation::create([
                'stagiaire_id' => $stagiaire->id,
                'quiz_id' => $quiz->id,
                'status' => 'completed',
                'started_at' => now(),
                'completed_at' => now(),
                'score' => 0,
                'correct_answers' => 0,
                'time_spent' => 0
            ]);

            $scoreTotal = 0;
            $correctCount = 0;
            $resultByQuestion = [];

            foreach ($quiz->questions()->where('type', 'rearrangement')->get() as $question) {
                $submittedAnswer = $userAnswers[$question->id] ?? [];

                // Sauvegarder la réponse
                QuizParticipationAnswer::create([
                    'participation_id' => $participation->id,
                    'question_id' => $question->id,
                    'answer_ids' => $submittedAnswer, // tableau ordonné d'IDs de réponses soumis
                ]);

                // Récupérer les bonnes réponses dans l'ordre correct
                $correctAnswers = $question->reponses()
                    // ->where('is_correct', true)
                    ->orderBy('position')
                    ->pluck('id')
                    ->toArray();

                // Vérifier si la réponse est correcte
                if ($submittedAnswer === $correctAnswers) {
                    $scoreTotal += intval($question->points);
                    $correctCount++;
                }

                $resultByQuestion[] = [
                    'question_id' => $question->id,
                    'is_correct' => $submittedAnswer === $correctAnswers,
                    'expected_order' => $correctAnswers,
                    'submitted_order' => $submittedAnswer
                ];
            }

            // Mise à jour des scores
            $participation->update([
                'score' => $scoreTotal,
                'correct_answers' => $correctCount
            ]);

            return [
                'participation' => $participation,
                'result_by_question' => $resultByQuestion
            ];
        });
    }
}
