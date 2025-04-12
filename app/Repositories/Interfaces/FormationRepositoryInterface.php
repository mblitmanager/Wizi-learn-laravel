<?php


namespace App\Repositories\Interfaces;
use App\Models\Formation;

interface FormationRepositoryInterface
{
    public function all();
    public function find($id): ? Formation;
    public function create(array $data): Formation;
    public function update($id, array $data): bool;
    public function delete($id): bool;
    public function getUniqueCategories(): \Illuminate\Support\Collection;

}
