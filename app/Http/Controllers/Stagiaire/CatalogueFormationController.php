<?php

namespace App\Http\Controllers\Stagiaire;

use App\Helpers\PaginationHelper;
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

    /**
     * Télécharger le PDF du cursus
     */
    public function downloadPdf($id)
    {
        try {
            $catalogueFormation = $this->catalogueFormationService->show($id);

            if (!$catalogueFormation || !$catalogueFormation->cursus_pdf) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier PDF n\'existe pas.'
                ], 404);
            }

            // Vérifier si le fichier existe dans le dossier public
            $filePath = base_path('public/' . $catalogueFormation->cursus_pdf);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier PDF n\'a pas été trouvé sur le serveur.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => asset($catalogueFormation->cursus_pdf),
                    'filename' => 'cursus_' . strtoupper($catalogueFormation->titre) . '.pdf'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du téléchargement du PDF.'
            ], 500);
        }
    }
}
