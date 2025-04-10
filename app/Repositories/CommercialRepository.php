<?php

namespace App\Repositories;

use App\Models\Commercial;
use App\Models\Formateur;
use App\Repositories\Interfaces\CommercialInterface;
use Illuminate\Support\Collection;

class CommercialRepository implements CommercialInterface
{
    public function all(): Collection
    {
        return Commercial::with('user', 'stagiaires')->get();
    }

    public function find(int $id): ?Commercial
    {
        return Commercial::with('user', 'stagiaires')->where('id', $id)->first();
    }

    public function create(array $data): Commercial
    {
        return Commercial::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $quiz = Commercial::findOrFail($id);
        return $quiz->update($data);
    }

    public function delete(int $id): bool
    {
        return Commercial::destroy($id) > 0;
    }
}
