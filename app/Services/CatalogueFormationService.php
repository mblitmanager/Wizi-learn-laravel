<?php

namespace App\Services;

use App\Models\CatalogueFormation;
use App\Models\Stagiaire;
use App\Repositories\Interfaces\CatalogueFormationInterface;


class CatalogueFormationService
{
    protected $catalogueFormationRepositoryInterface;

    public function __construct(CatalogueFormationInterface $catalogueFormationRepositoryInterface)
    {
        $this->catalogueFormationRepositoryInterface = $catalogueFormationRepositoryInterface;
    }

    /**
     * List catalogue formations, optionally filtered by formation_id
     *
     * @param int|null $formationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list($formationId = null)
    {
        // Prefer repository if available
        try {
            if ($this->catalogueFormationRepositoryInterface) {
                // If repository supports filters, we defer; otherwise fallback to model
                if (method_exists($this->catalogueFormationRepositoryInterface, 'allWithFilters')) {
                    return $this->catalogueFormationRepositoryInterface->allWithFilters(['formation_id' => $formationId]);
                }
            }
        } catch (\Throwable $e) {
            // ignore and fallback to model
        }

        // Fallback: use the Eloquent model directly
        $query = \App\Models\CatalogueFormation::with('formation');
        if ($formationId) {
            $query->where('formation_id', $formationId);
        }

        return $query->get();
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

        // Récupérer le stagiaire avec ses catalogues et formations via la table pivot
        $stagiaire = Stagiaire::with(['catalogue_formations.formation'])->find($stagiaireId);

        if (!$stagiaire) {
            throw new \Exception("Stagiaire introuvable");
        }

        // On récupère tous les catalogues liés au stagiaire (avec formation déjà chargée)
        $catalogues = $stagiaire->catalogue_formations;

        // Structure de retour : chaque entrée contient le pivot, le catalogue et la formation associée
        $result = $catalogues->map(function ($catalogue) {
            return [
                'pivot' => $catalogue->pivot ? $catalogue->pivot->toArray() : null,
                'catalogue' => $catalogue->toArray(),
                'formation' => $catalogue->formation ? $catalogue->formation->toArray() : null,
            ];
        });

        return [
            'stagiaire' => $stagiaire->toArray(),
            'catalogues' => $result,
        ];
    }
}
