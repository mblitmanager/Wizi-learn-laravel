<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\FormationService;
use Illuminate\Support\Facades\Validator;
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
    public function getFormationsByCategory($category)
    {
        $formations = $this->formationService->getFormationsByCategory($category);
        return response()->json($formations);
    }

    public function getFormations()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            if ($user->role != 'formateur' && $user->role != 'admin') {
                // Vérifier si l'utilisateur est associé à ce stagiaire
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }
            $formations = $this->formationService->getFormationsByStagiaire($user->stagiaire->id);

            return response()->json([
                'data' => $formations
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
    }

    public function updateImage(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:102400',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::findOrFail($id);

            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('uploads/users'), $imageName);
                $user->image = 'uploads/users/' . $imageName;
                $user->save();
            }

            return response()->json([
                'message' => 'Image mise à jour avec succès',
                'image' => $user->image,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
