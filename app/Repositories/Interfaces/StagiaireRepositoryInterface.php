<?php
namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;
use App\Models\Stagiaire;

interface StagiaireRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Stagiaire;
    public function create(array $data): Stagiaire;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function desactive(int $id): bool;
    public function active(int $id): bool;
    public function getStagiaireStats($id);
    public function getStagiaireRankings($id);
    public function getStagiaireProgress($id);
}