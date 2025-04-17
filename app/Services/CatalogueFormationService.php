<?php

namespace App\Services;

use App\Repositories\Interfaces\CatalogueFormationInterface;


class CatalogueFormationService
{
    protected $catalogueFormationRepositoryInterface;

    public function __construct(CatalogueFormationInterface $catalogueFormationRepositoryInterface)
    {
        $this->catalogueFormationRepositoryInterface = $catalogueFormationRepositoryInterface;
    }

    public function list()
    {
        return $this->catalogueFormationRepositoryInterface->all();
    }

    public function show($id)
    {
        return $this->catalogueFormationRepositoryInterface->find($id);
    }
    public function create(array $data)
    {
        // Créer le quiz
        return $this->catalogueFormationRepositoryInterface->create($data);
    }

    public function update(int $id, array $data)
    {
        $catalogueFormation = $this->catalogueFormationRepositoryInterface->find($id);

        if (!$catalogueFormation) {
            throw new \Exception("Quiz not found");
        }

        // Mettre à jour le quiz
        return $this->catalogueFormationRepositoryInterface->update($id, $data);
    }

    public function delete($id)
    {
        return $this->catalogueFormationRepositoryInterface->delete($id);
    }
}
