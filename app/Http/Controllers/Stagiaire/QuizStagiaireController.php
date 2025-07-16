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

}
