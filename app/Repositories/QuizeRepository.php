<?php

namespace App\Repositories;

use App\Models\Quiz;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Support\Collection;

class QuizeRepository implements QuizRepositoryInterface
{
    public function all(): Collection
    {
        return Quiz::with('participations', 'questions')->get();
    }

    public function find($id): ?Quiz
    {
        return Quiz::with('participations', 'questions')->find($id);
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
}
