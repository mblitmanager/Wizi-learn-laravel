<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StagiaireController extends Controller
{
    public function getFormationsByStagiaire($id)
    {
        try {
            $stagiaire = Stagiaire::with(['catalogue_formations.formation'])
                ->findOrFail($id);

            $formations = $stagiaire->catalogue_formations->map(function ($catalogue) {

                return [
                    'id' => (string)$catalogue->id,
                    'titre' => $catalogue->titre,
                    'description' => $catalogue->description,
                    'categorie' => $catalogue->formation->categorie,
                    'prerequis' => $catalogue->prerequis,
                    'duree' => $catalogue->duree,
                    'prix' => $catalogue->tarif,
                    'image' => $catalogue->image_url,
                    'status' => $catalogue->status,
                    'certification' => $catalogue->certification,
                    'created_at' => $catalogue->created_at,
                    'updated_at' => $catalogue->updated_at
                ];
            });

            return response()->json($formations);
        } catch (\Exception $e) {
            Log::error('Erreur dans getFormationsByStagiaire', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Une erreur est survenue lors de la récupération des formations'
            ], 500);
        }
    }

    public function setOnboardingSeen(Request $request)
    {
        $user = auth()->user();
        $stagiaire = $user->stagiaire; // ou autre logique pour récupérer le stagiaire
        $stagiaire->onboarding_seen = true;
        $stagiaire->save();

        return response()->json(['success' => true]);
    }

    /**
     * Met à jour la photo de profil de l'utilisateur connecté (champ image de User)
     */
    public function updateProfilePhoto(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
        }
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'user_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/users', $imageName);
            $user->image = 'users/' . $imageName;
            $user->save();
            return response()->json(['success' => true, 'image' => $user->image]);
        }
        return response()->json(['success' => false, 'error' => 'Aucun fichier image reçu'], 400);
    }
}
