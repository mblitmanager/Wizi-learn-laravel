<?php

namespace App\Repositories\Interfaces;

use App\Models\Quiz;
use Illuminate\Support\Collection;

interface QuizRepositoryInterface
{
    public function all();
    public function find($id): ?Quiz;
    public function create(array $data): Quiz;
    public function update($id, array $data): bool;
    public function delete($id): bool;
    public function getQuestionsByQuizId($quizId): Collection;
    public function getQuizzesByStagiaire($stagiaireId): Collection;
    public function getUniqueCategories(): Collection;
    public function submitQuizAnswers($quizId, $stagiaireId, array $answers): array;
}
