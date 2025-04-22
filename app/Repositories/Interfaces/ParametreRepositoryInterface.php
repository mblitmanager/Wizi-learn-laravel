<?php


namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Support\Collection;

interface ParametreRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): User;
    public function delete(int $id): bool;
}
