<?php

namespace App\Repositories;

use App\Models\Quiz;
use App\Models\Questions;
use App\Models\Reponse;
use App\Models\Participation;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuizeRepository implements QuizRepositoryInterface
{
    public function all(): Collection
    {
        return Quiz::with(['participations', 'questions.reponses', 'formation'])->get();
    }

    public function find($id): ?Quiz
    {
        return Quiz::with(['participations', 'questions.reponses', 'formation'])->find($id);
    }

    public function create(array $data): Quiz
    {
        return Quiz::create($data);
    }

    public function update($id, array $data): bool
    {
        $quiz = Quiz::findOrFail($id);
        return $quiz->update($data);
    }

    public function delete($id): bool
    {
        return Quiz::destroy($id) > 0;
    }

    public function getQuizzesByStagiaire($stagiaireId): Collection
    {
        return Quiz::whereHas('formation', function($query) use ($stagiaireId) {
            $query->whereHas('stagiaires', function($q) use ($stagiaireId) {
                $q->where('stagiaires.id', $stagiaireId);
            });
        })->get();
    }

    public function getQuizQuestions($quizId): Collection
    {
        return Questions::where('quiz_id', $quizId)
            ->with('reponses')
            ->get();
    }

    public function submitQuizAnswers($quizId, $stagiaireId, array $answers): array
    {
        DB::beginTransaction();
        try {
            $quiz = $this->find($quizId);
            $questions = $this->getQuizQuestions($quizId);
            
            $score = 0;
            $totalQuestions = $questions->count();
            
            foreach ($answers as $questionId => $reponseId) {
                $question = $questions->firstWhere('id', $questionId);
                $reponse = $question->reponses->firstWhere('id', $reponseId);
                
                if ($reponse && $reponse->isCorrect) {
                    $score++;
                }
            }
            
            $percentage = ($score / $totalQuestions) * 100;
            
            // Enregistrer la participation
            $participation = Participation::create([
                'quiz_id' => $quizId,
                'stagiaire_id' => $stagiaireId,
                'score' => $percentage,
                'completed' => true
            ]);
            
            DB::commit();
            
            return [
                'score' => $percentage,
                'total_questions' => $totalQuestions,
                'correct_answers' => $score,
                'participation_id' => $participation->id
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getQuizProgress($quizId, $stagiaireId): array
    {
        $participation = Participation::where('quiz_id', $quizId)
            ->where('stagiaire_id', $stagiaireId)
            ->first();
            
        if (!$participation) {
            return [
                'completed' => false,
                'score' => 0,
                'attempts' => 0
            ];
        }
        
        return [
            'completed' => $participation->completed,
            'score' => $participation->score,
            'attempts' => $participation->attempts,
            'last_attempt' => $participation->updated_at
        ];
    }

    public function getQuizStats($quizId): array
    {
        $quiz = $this->find($quizId);
        
        return [
            'total_participations' => $quiz->participations->count(),
            'average_score' => $quiz->participations->avg('score'),
            'highest_score' => $quiz->participations->max('score'),
            'lowest_score' => $quiz->participations->min('score'),
            'completion_rate' => ($quiz->participations->where('completed', true)->count() / $quiz->participations->count()) * 100
        ];
    }

    public function getQuizzesByCategory($categoryId): Collection
    {
        return Quiz::whereHas('formation', function($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })->get();
    }
}
