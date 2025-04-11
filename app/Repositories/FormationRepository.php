<?php


namespace App\Repositories;


use App\Models\Formation;
use App\Repositories\Interfaces\FormationRepositoryInterface;

class FormationRepository implements FormationRepositoryInterface
{
    public function all()
    {
        return Formation::with(['formateurs', 'stagiaires'])->get();
    }

    public function find($id): ?Formation
    {
        return Formation::with(['formateurs', 'stagiaires'])->find($id);
    }

    public function create(array $data): Formation
    {
        return Formation::create($data);
    }

    public function update($id, array $data): bool
    {
        $formation = Formation::find($id);
        return $formation ? $formation->update($data) : false;
    }

    public function delete($id): bool
    {
        $formation = Formation::find($id);
        return $formation ? $formation->delete() : false;
    }
}
