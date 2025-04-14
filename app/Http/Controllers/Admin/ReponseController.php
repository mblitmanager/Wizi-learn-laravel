<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reponse;
use App\Models\Questions;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReponseController extends Controller
{
    /**
     * Récupérer toutes les réponses d'une question spécifique
     *
     * @param int $questionId
     * @return JsonResponse
     */
    public function getReponsesByQuestion(int $questionId): JsonResponse
    {
        $question = Questions::findOrFail($questionId);
        $reponses = Reponse::where('question_id', $questionId)->get();

        return response()->json([
            'success' => true,
            'data' => $reponses
        ]);
    }
}
