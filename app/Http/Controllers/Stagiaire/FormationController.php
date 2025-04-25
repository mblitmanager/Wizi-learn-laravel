<?php

namespace App\Http\Controllers\Stagiaire;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Services\FormationService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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
            $formations = $this->formationService->getFormationsByStagiaire($id);

            return response()->json([
                'data' => $formations
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
