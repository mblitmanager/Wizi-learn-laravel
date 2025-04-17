<?php

namespace App\Repositories\Interfaces;

use App\Models\CatalogueFormation;
use Illuminate\Support\Collection;

interface CatalogueFormationInterface
{
    public function all(): Collection;
    public function find(int $id): ?CatalogueFormation;
    public function create(array $data): CatalogueFormation;
    public function update(int $id, array $data): CatalogueFormation;
    public function delete(int $id): bool;
}
