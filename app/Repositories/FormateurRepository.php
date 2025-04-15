<?php

namespace App\Repositories;

use App\Models\Formateur;
use App\Repositories\Interfaces\FormateurInterface;
use Illuminate\Support\Collection;

class FormateurRepository implements FormateurInterface
{
    public function all(): Collection
    {
        return Formateur::with('user', 'formations')->get();
    }

    public function find(int $id): ?Formateur
    {
        return Formateur::with('user', 'formations', 'stagiaires')->where('id', $id)->first();
    }

    public function create(array $data): Formateur
    {
        return Formateur::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $formateur = Formateur::findOrFail($id);
        return $formateur->update($data);
    }

    public function delete(int $id): bool
    {
        return Formateur::destroy($id) > 0;
    }
}
