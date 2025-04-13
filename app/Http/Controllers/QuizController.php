<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\QuizeRepository;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected $quizRepository;

    public function __construct(QuizeRepository $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    public function getQuizzesByCategory($categoryId)
    {
        $quizzes = $this->quizRepository->getQuizzesByCategory($categoryId);
        return response()->json($quizzes);
    }

    public function getCategories()
    {
        $categories = $this->quizRepository->getUniqueCategories();
        return response()->json($categories);
    }
} 