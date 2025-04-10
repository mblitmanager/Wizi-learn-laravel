<?php
namespace App\Repositories\Interfaces;

use App\Models\Commercial;
use Illuminate\Support\Collection;

interface CommercialInterface
{
    public function all(): Collection;
    public function find(int $id): ?Commercial;
    public function create(array $data): Commercial;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}