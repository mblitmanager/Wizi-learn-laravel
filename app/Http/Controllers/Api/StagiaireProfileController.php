<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class StagiaireProfileController extends Controller
{
    /**
     * Update stagiaire profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            // Get authenticated user
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user->stagiaire) {
                return response()->json([
                    'error' => 'Aucun profil stagiaire trouvé pour cet utilisateur'
                ], 404);
            }

            $stagiaire = $user->stagiaire;

            // Validation
            $validator = Validator::make($request->all(), [
                'prenom' => 'required|string|min:2|max:255',
                'nom' => 'required|string|min:2|max:255',
                'telephone' => 'nullable|string|regex:/^0[0-9]{9}$/|max:20',
                'ville' => 'nullable|string|max:255',
                'code_postal' => 'nullable|string|regex:/^[0-9]{5}$/|max:10',
                'adresse' => 'nullable|string|max:255',
            ], [
                'prenom.required' => 'Le prénom est obligatoire',
                'prenom.min' => 'Le prénom doit contenir au moins 2 caractères',
                'nom.required' => 'Le nom est obligatoire',
                'nom.min' => 'Le nom doit contenir au moins 2 caractères',
                'telephone.regex' => 'Le numéro de téléphone doit être au format 0XXXXXXXXX (10 chiffres)',
                'code_postal.regex' => 'Le code postal doit contenir 5 chiffres',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update stagiaire data
            $stagiaire->prenom = $request->prenom;
            $stagiaire->ville = $request->ville;
            $stagiaire->code_postal = $request->code_postal;
            $stagiaire->adresse = $request->adresse;
            $stagiaire->telephone = $request->telephone;
            $stagiaire->save();

            // Update user name
            $user->name = $request->nom;
            $user->save();

            // Reload stagiaire with user
            $stagiaire->load('user');

            return response()->json([
                'message' => 'Profil mis à jour avec succès',
                'stagiaire' => [
                    'id' => $stagiaire->id,
                    'prenom' => $stagiaire->prenom,
                    'nom' => $user->name,
                    'telephone' => $stagiaire->telephone,
                    'ville' => $stagiaire->ville,
                    'code_postal' => $stagiaire->code_postal,
                    'adresse' => $stagiaire->adresse,
                    'email' => $user->email,
                    'image' => $user->image,
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error updating profile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la mise à jour du profil',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current stagiaire profile
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user->stagiaire) {
                return response()->json([
                    'error' => 'Aucun profil stagiaire trouvé'
                ], 404);
            }

            $stagiaire = $user->stagiaire;
            $stagiaire->load('user');

            return response()->json([
                'id' => $stagiaire->id,
                'prenom' => $stagiaire->prenom,
                'nom' => $user->name,
                'telephone' => $stagiaire->telephone,
                'ville' => $stagiaire->ville,
                'code_postal' => $stagiaire->code_postal,
                'adresse' => $stagiaire->adresse,
                'email' => $user->email,
                'image' => $user->image,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error getting profile', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
