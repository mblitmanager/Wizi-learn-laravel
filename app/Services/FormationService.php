<?php


namespace App\Services;


use App\Repositories\Interfaces\FormationRepositoryInterface;

class FormationService
{
    protected $formationRepository;

    public function __construct(FormationRepositoryInterface $formationRepository)
    {
        $this->formationRepository = $formationRepository;
    }

    public function getAll()
    {
        return $this->formationRepository->all();
    }

    public function getById($id)
    {
        return $this->formationRepository->find($id);
    }

    public function store(array $data)
    {
        return $this->formationRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->formationRepository->update($id, $data);
    }

    public function destroy($id)
    {
        return $this->formationRepository->delete($id);
    }

    public function getUniqueCategories()
    {
        return $this->formationRepository->getUniqueCategories();
    }
    public function getFormationsByCategory($category)
    {
        return $this->formationRepository->all()->where('categorie', $category);
    }

    public function getFormationsByStagiaire($stagiaireId)
    {
        return $this->formationRepository->getFormationsByStagiaire($stagiaireId);
    }
}
