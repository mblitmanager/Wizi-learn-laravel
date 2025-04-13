<?php

namespace App\Http\Controllers\Stagiaire;

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
} 