<?php

namespace App\Repositories;

use App\Models\CatalogueFormation;
use App\Repositories\Interfaces\CatalogueFormationInterface;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Log;
use App\Models\Formateur;

class CatalogueFormationRepository implements CatalogueFormationInterface
{
    /**
     * Récupérer toutes les entrées de la table catalogue_formations.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return CatalogueFormation::with(['formation', 'formateurs', 'stagiaires'])
            ->where('statut', 1)
            ->get();
    }
    /**
     * Trouver une entrée par son ID.
     *
     * @param int $id
     * @return CatalogueFormation|null
     */
    public function find(int $id): ?CatalogueFormation
    {
        return CatalogueFormation::with(['formation', 'formateurs', 'stagiaires'])
            ->where('id', $id)
            ->first();
    }

    /**
     * Créer une nouvelle entrée dans la table catalogue_formations.
     *
     * @param array $data
     * @return CatalogueFormation
     */
    public function create(array $data): CatalogueFormation
    {
        return CatalogueFormation::create($data);
    }

    /**
     * Mettre à jour une entrée existante dans la table catalogue_formations.
     *
     * @param int $id
     * @param array $data
     * @return CatalogueFormation
     */
    public function update(int $id, array $data): CatalogueFormation
    {
        $catalogueFormation = CatalogueFormation::findOrFail($id);
        $catalogueFormation->update($data);
        return $catalogueFormation;
    }


    public function updateFormateur(int $id, array $data): bool
    {
        $formateur = Formateur::findOrFail($id);
        return $formateur->update($data);
    }
    /**
     * Supprimer une entrée de la table catalogue_formations.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $catalogueFormation = CatalogueFormation::findOrFail($id);
        return $catalogueFormation->delete();
    }
}
