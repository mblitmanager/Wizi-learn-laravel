<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\CatalogueFormation;
use App\Services\CatalogueFormationService;
use Illuminate\Http\Request;

class CatalogueFormationController extends Controller
{
    protected $catalogueFormationService;
    public function __construct(CatalogueFormationService $catalogueFormationService)
    {
        $this->catalogueFormationService = $catalogueFormationService;
    }

    public function getAllCatalogueFormations()
    {
        $catalogueFormations = $this->catalogueFormationService->list();

        return response()->json($catalogueFormations);
    }

    public function getCatalogueFormationById($id)
    {
        $catalogueFormation = CatalogueFormation::with('formation')->find($id);

        if (!$catalogueFormation) {
            return response()->json(['error' => 'Catalogue de formation introuvable'], 404);
        }

        // Récupérer l'ID de la formation associée
        $formationId = $catalogueFormation->formation_id;

        return response()->json([
            'catalogueFormation' => $catalogueFormation,
            'formationId' => $formationId,
        ]);
    }
    public function getFormationsAndCatalogues($stagiaireId)
    {
        $stagiaire = $this->catalogueFormationService->getFormationsAndCatalogues($stagiaireId);
        return response()->json($stagiaire);
    }
}
