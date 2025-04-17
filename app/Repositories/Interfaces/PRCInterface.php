<?php

namespace App\Repositories\Interfaces;
use Illuminate\Support\Collection;
use App\Models\PoleRelationClient;

interface PRCInterface
{
    public function all(): Collection;
    public function find(int $id): ?PoleRelationClient;
    public function create(array $data): PoleRelationClient;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
