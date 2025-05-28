<?php


namespace App\Repositories;


use App\Models\Formation;
use App\Repositories\Interfaces\FormationRepositoryInterface;

class FormationRepository implements FormationRepositoryInterface
{
    public function all()
    {
        return Formation::with(['formateurs', 'catalogueFormation'])->get();
    }

    public function find($id): ?Formation
    {
        return Formation::with(['formateurs', 'catalogueFormation'])->find($id);
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

    public function getUniqueCategories(): \Illuminate\Support\Collection
    {
        return Formation::select('categorie')->distinct()->pluck('categorie');
    }

    public function getFormationsByStagiaire($stagiaireId): \Illuminate\Support\Collection
    {
        return Formation::with([
            'catalogueFormation.stagiaires',
            'quizzes.questions.reponses'
        ])
            ->whereHas('catalogueFormation', function($query) use ($stagiaireId) {
                $query->whereHas('stagiaires', function($q) use ($stagiaireId) {
                    $q->where('stagiaires.id', $stagiaireId);
                });
            })
            ->get();
    }
}
