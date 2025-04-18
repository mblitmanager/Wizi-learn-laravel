<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
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
        $category = $this->catalogueFormationService->getCatalogueFormationById($id);
        return response()->json($category);
    }
    public function getFormationsAndCatalogues($stagiaireId)
    {
        $stagiaire = $this->catalogueFormationService->getFormationsAndCatalogues($stagiaireId);
        return response()->json($stagiaire);
    }
}
