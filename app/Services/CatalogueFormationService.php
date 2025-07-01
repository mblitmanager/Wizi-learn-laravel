<?php

namespace App\Services;

use App\Models\Stagiaire;
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

    public function getCatalogueFormationById($id)
    {
        return $this->catalogueFormationRepositoryInterface->find($id);
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

    public function getFormationsAndCatalogues(int $stagiaireId)
    {
        // Récupérer le stagiaire avec ses relations
        $stagiaire = Stagiaire::with(['catalogue_formations'])->find($stagiaireId);

        if (!$stagiaire) {
            throw new \Exception("Stagiaire not found");
        }

        // Récupérer les formations
        $catalogues = $stagiaire->catalogue_formations;

        // // Récupérer les catalogues de formation associés aux formations
        // $catalogues = $formations->map(function ($formation) {
        //     return $formation->catalogueFormation;
        // })->filter(); // Filtrer les catalogues non nulls

        return [
            'stagiaire' => $stagiaire,
            'catalogues' => $catalogues,
        ];
    }
}
