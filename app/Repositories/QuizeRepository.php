<?php

namespace App\Repositories;

use App\Models\Quiz;
use App\Models\Questions;
use App\Models\Reponse;
use App\Models\Participation;
use App\Models\QuizParticipation;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Formation;

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

    public function getQuizzesByStagiaire($stagiaireId, $withQuestions = true): Collection
    {
        $query = Quiz::where('status', 'actif')
            ->whereHas('formation', function ($query) use ($stagiaireId) {
                $query->whereHas('catalogueFormation', function ($q) use ($stagiaireId) {
                    $q->whereHas('stagiaires', function ($q) use ($stagiaireId) {
                        $q->where('stagiaires.id', $stagiaireId);
                    });
                });
            })
            ->with(['formation'])
            ->withSum('questions', 'points');

        if ($withQuestions) {
            $query->with('questions.reponses');
        }

        return $query->get();
    }

    /**
     * Récupère les quiz du stagiaire avec les participations de l'utilisateur
     * (optimisé pour éviter les requêtes N+1)
     */
    public function getQuizzesWithUserParticipations($stagiaireId, $userId, $withQuestions = true): Collection
    {
        $quizzes = $this->getQuizzesByStagiaire($stagiaireId, $withQuestions);
        $quizIds = $quizzes->pluck('id')->toArray();

        // Récupérer TOUTES les participations de l'utilisateur pour ces quiz en une seule requête
        $participations = QuizParticipation::where('user_id', $userId)
            ->whereIn('quiz_id', $quizIds)
            ->orderByDesc('created_at')
            ->get();

        // Indexer pour retrouver rapidement par quiz_id (garder la plus récente)
        $participationsByQuizId = [];
        foreach ($participations as $p) {
            if (!isset($participationsByQuizId[$p->quiz_id])) {
                $participationsByQuizId[$p->quiz_id] = $p;
            }
        }

        // Attacher les participations aux quiz
        $quizzes->each(function ($quiz) use ($participationsByQuizId) {
            $quiz->setAttribute(
                'user_last_participation',
                $participationsByQuizId[$quiz->id] ?? null
            );
        });

        return $quizzes;
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
            $submittedAnswersData = [];

            foreach ($answers as $questionId => $submittedAnswers) {
                $question = $questions->firstWhere('id', $questionId);

                foreach ($submittedAnswers as $submittedAnswer) {
                    $reponseId = $submittedAnswer['id'] ?? null;
                    $reponseText = $submittedAnswer['text'] ?? null;

                    $reponse = $question->reponses->firstWhere('id', $reponseId);

                    if ($reponse) {
                        $submittedAnswersData[] = [
                            'question_id' => $questionId,
                            'reponse_id' => $reponseId,
                            'reponse_text' => $reponseText,
                            'is_correct' => $reponse->isCorrect
                        ];

                        // Correct answer validation
                        if ($reponse->isCorrect) {
                            $score++;
                        }
                    } else {
                        // Handle cases where the answer text matches but the ID is missing
                        $correctReponse = $question->reponses->firstWhere('text', $reponseText);
                        if ($correctReponse && $correctReponse->isCorrect) {
                            $score++;
                        }
                    }
                }
            }

            $percentage = ($score / $totalQuestions) * 100;

            // Enregistrer la participation avec les réponses soumises
            $participation = Participation::create([
                'quiz_id' => $quizId,
                'stagiaire_id' => $stagiaireId,
                'score' => $percentage,
                'completed' => true,
                'submitted_answers' => json_encode($submittedAnswersData) // Store submitted answers as JSON
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
        return Quiz::whereHas('formation', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })->get();
    }

    public function getUniqueCategories(): Collection
    {
        return Formation::select('categorie as name', 'slug', 'icon', 'description')
            ->distinct()
            ->get()
            ->map(function ($category) {
                $formations = Formation::where('categorie', $category->name)->get();
                return [
                    'id' => $category->slug,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon' => $category->icon ?? 'file-text',
                    'description' => $category->description ?? '',
                    'formations' => $formations
                ];
            });
    }

    public function getQuestionsByQuizId($quizId): Collection
    {
        return Questions::where('quiz_id', $quizId)
            ->with('reponses')
            ->get();
    }
}
