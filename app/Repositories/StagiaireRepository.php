<?php

namespace App\Repositories;

use App\Models\Stagiaire;
use App\Repositories\Interfaces\StagiaireRepositoryInterface;
use Illuminate\Support\Collection;

class StagiaireRepository implements StagiaireRepositoryInterface
{
    public function all(): Collection
    {
        return Stagiaire::with('user', 'formations')->get();
    }

    public function find(int $id): ?Stagiaire
    {
        return Stagiaire::with('user', 'formations')->find($id);
    }

    public function create(array $data): Stagiaire
    {
        return Stagiaire::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $stagiaire = Stagiaire::findOrFail($id);
        return $stagiaire->update($data);
    }

    public function delete(int $id): bool
    {
        return Stagiaire::destroy($id) > 0;
    }

    public function desactive(int $id): bool
    {
        $stagiaire = Stagiaire::findOrFail($id);
        return $stagiaire->update(['statut' => '0']);
    }

    public function active(int $id): bool
    {
        $stagiaire = Stagiaire::findOrFail($id);
        return $stagiaire->update(['statut' => '1']);
    }
}
