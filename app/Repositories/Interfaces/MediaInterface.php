<?php

namespace App\Repositories\Interfaces;

use App\Models\Media;
use Illuminate\Support\Collection;

interface MediaInterface
{
    public function all(): Collection;
    public function find(int $id): ?Media;
    public function create(array $data): Media;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
