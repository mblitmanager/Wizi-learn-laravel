<?php
namespace App\Repositories\Interfaces;

use App\Models\Formateur;
use Illuminate\Support\Collection;

interface FormateurInterface
{
    public function all(): Collection;
    public function find(int $id): ?Formateur;
    public function create(array $data): Formateur;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}