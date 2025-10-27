<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizParticipation;
use App\Models\QuizParticipationAnswer;
use App\Models\Stagiaire;
use App\Models\User;
use App\Services\QuizService;
use Illuminate\Container\Attributes\Auth;
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

            // Tenter de récupérer la dernière participation de l'utilisateur pour ce quiz
            // Priorité : Participation (avec stagiaire_id) — fallback vers QuizParticipation (user_id)
            $participation = null;
            try {
                if (isset($user->stagiaire) && $user->stagiaire && isset($user->stagiaire->id)) {
                    $stagiaireId = $user->stagiaire->id;
                    $participation = \App\Models\Participation::where('stagiaire_id', $stagiaireId)
                        ->where('quiz_id', $quizId)
                        ->where('deja_jouer', true)
                        ->latest()
                        ->first();
                }
            } catch (\Throwable $e) {
                // ignore and fallback
            }

            if (!$participation) {
                $participation = \App\Models\QuizParticipation::where('user_id', $user->id)
                    ->where('quiz_id', $quizId)
                    ->where('completed', true)
                    ->latest()
                    ->first();
            }

            $playedQuestions = [];
            if ($participation) {
                // Récupérer les réponses liées à la participation
                // Try both QuizParticipationAnswer and QuizParticipationAnswer-like tables
                $answers = \App\Models\QuizParticipationAnswer::where('participation_id', $participation->id)->get();
                if ($answers->isEmpty()) {
                    // Some schema stores answers as JSON on Participation model (submitted_answers)
                    if (isset($participation->submitted_answers) && !empty($participation->submitted_answers)) {
                        $decoded = json_decode($participation->submitted_answers, true);
                        $answerMap = [];
                        if (is_array($decoded)) {
                            foreach ($decoded as $item) {
                                if (isset($item['question_id'])) {
                                    $answerMap[$item['question_id']] = $item['reponse_id'] ?? ($item['answer_ids'] ?? []);
                                }
                            }
                        }
                    } else {
                        $answerMap = [];
                    }
                } else {
                    $answerMap = [];
                    foreach ($answers as $a) {
                        $answerMap[$a->question_id] = $a->answer_ids ?? [];
                    }
                }

                // Parcourir les questions et ajouter l'objet question complet si présent
                foreach ($questions as $q) {
                    $qid = $q->id;
                    if (isset($answerMap[$qid]) && !empty($answerMap[$qid])) {
                        // Inclure l'objet question complet (id, texte, reponses)
                        $playedQuestions[] = [
                            'question' => [
                                'id' => $q->id,
                                'text' => $q->texte ?? ($q->text ?? ($q->question ?? '')),
                                'reponses' => $q->reponses ?? []
                            ],
                            'selectedAnswers' => $answerMap[$qid]
                        ];
                    }
                }
            }

            return response()->json([
                'data' => $questions,
                'playedQuestions' => $playedQuestions,
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

            // Correction : s'assurer que timeSpent n'est jamais négatif
            $timeSpent = $request->input('timeSpent', 0);
            $timeSpent = max(0, (int)$timeSpent);
            $request->merge(['timeSpent' => $timeSpent]);

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

    // Ajout d'une méthode pour le score moyen stagiaire (exemple)
    public function getStagiaireAverageScore($stagiaireId)
    {
        $classements = \App\Models\Classement::where('stagiaire_id', $stagiaireId)->get();
        $totalPoints = $classements->sum('points');
        $quizCount = $classements->count();
        $averageScore = $quizCount > 0 ? round($totalPoints / $quizCount, 2) : 0;
        return response()->json([
            'totalQuizzes' => $quizCount,
            'averageScore' => $averageScore,
            'totalPoints' => $totalPoints
        ]);
    }
}
