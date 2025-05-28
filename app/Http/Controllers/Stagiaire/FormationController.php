<?php

namespace App\Http\Controllers\Stagiaire;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Models\CatalogueFormation;
use App\Models\Formation;
use App\Models\Stagiaire;
use App\Services\FormationService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class FormationController extends Controller
{
    protected $formationService;

    public function __construct(FormationService $formationService)
    {
        $this->formationService = $formationService;
    }

    public function getFormationsByStagiaireId($id)
    {
        try {
            // Vérifie d'abord si le stagiaire existe
            $stagiaire = Stagiaire::find($id);

            if (!$stagiaire) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stagiaire non trouvé'
                ], 404);
            }

            // Récupère les formations à travers les catalogues de formation
            $formations = Formation::whereHas('catalogueFormation', function ($query) use ($id) {
                $query->whereHas('stagiaires', function ($q) use ($id) {
                    $q->where('stagiaires.id', $id);
                });
            })
            ->with(['medias', 'catalogueFormation'])
            ->get();

            return response()->json([
                'success' => true,
                'data' => $formations
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans getFormationsByStagiaireId', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Une erreur est survenue lors de la récupération des formations'
            ], 500);
        }
    }

    public function getAllFormations()
    {
        try {
            $formations = $this->formationService->getAll();
            $pagianted = PaginationHelper::paginate($formations, 10);
            return response()->json([
                'data' => $pagianted
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
