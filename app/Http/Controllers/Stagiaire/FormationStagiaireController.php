<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use Illuminate\Http\Request;
use App\Services\FormationService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "JWT Authentication API",
    version: "1.0.0",
    description: "API documentation for JWT-based authentication"
)]

class FormationStagiaireController extends Controller
{
    protected $formationService;

    public function __construct(FormationService $formationService)
    {
        $this->formationService = $formationService;
    }


    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Formation $formation)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, Formation $formation)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Formation $formation)
    // {
    //     //
    // }

    public function getCategories()
    {
        $categories = $this->formationService->getUniqueCategories();
        return response()->json($categories);
    }
    public function getFormationsByCategory($categoryId)
    {
        $formations = $this->formationService->getFormationsByCategory($categoryId);
        return response()->json($formations);
    }
}
