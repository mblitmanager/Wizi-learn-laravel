<?php

namespace App\Repositories;

use App\Models\CatalogueFormation;
use App\Repositories\Interfaces\CatalogueFormationInterface;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Log;

class CatalogueFormationRepository implements CatalogueFormationInterface
{
    /**
     * Récupérer toutes les entrées de la table catalogue_formations.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return CatalogueFormation::with('formation')->get();
    }
    /**
     * Trouver une entrée par son ID.
     *
     * @param int $id
     * @return CatalogueFormation|null
     */
    public function find(int $id): ?CatalogueFormation
    {
        return CatalogueFormation::with('formation')->where('id', $id)->first();
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
