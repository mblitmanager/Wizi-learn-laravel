<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\QuizService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

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
            // Vérifier si l'utilisateur est bien le stagiaire demandé
            if ($user->id != $stagiaireId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $quizzes = $this->quizService->getQuizzesByStagiaire($stagiaireId);
            return response()->json($quizzes);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getCategories()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $categories = $this->quizService->getCategories();
            return response()->json($categories);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function getQuestionsByQuizId($quizId)
    {
        $questions = $this->quizService->getQuestionsByQuizId($quizId);
        return response()->json($questions);
    }

    public function submitQuiz(Request $request, $quizId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $result = $this->quizService->submitQuizAnswers($quizId, $user->id, $request->all());
            return response()->json($result);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
