<?php
namespace App\Repositories\Interfaces;

use App\Models\Quiz;
use Illuminate\Support\Collection;

interface QuizRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Quiz;
    public function create(array $data): Quiz;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}