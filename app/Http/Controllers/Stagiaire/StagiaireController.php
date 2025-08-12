<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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
        $user = Auth::user();
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
        $user = \App\Models\User::find(Auth::id());
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
        }
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            // Stocker directement dans public/uploads/users (accessible sans symlink)
            $destinationPath = public_path('uploads/users');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $imageName);
            $user->image = 'uploads/users/' . $imageName;
            $user->save();
            $imageUrl = asset('uploads/users/' . $imageName);
            return response()->json(['success' => true, 'image' => $imageUrl]);
        }
        return response()->json(['success' => false, 'error' => 'Aucun fichier image reçu'], 400);
    }
}
