<?php

namespace App\Http\Controllers\Stagiaire;

use App\Models\User;
use App\Models\Stagiaire;
use App\Models\ParrainageToken;
use App\Models\Parrainage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ParrainageController extends Controller
{

    // Générer un lien de parrainage
    public function generateLink(Request $request)
    {
        $user = $request->user();

        $token = Str::random(40);

        ParrainageToken::create([
            'token' => $token,
            'user_id' => $user->id,
            'parrain_data' => json_encode([
                'user' => $user,
                'stagiaire' => $user->stagiaire
            ]),
            'expires_at' => now()->addDays(30)
        ]);

        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }

    // Récupérer les données du parrain
    public function getParrainData($token)
    {
        $parrainage = ParrainageToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$parrainage) {
            return response()->json([
                'success' => false,
                'message' => 'Lien de parrainage invalide ou expiré'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'parrain' => json_decode($parrainage->parrain_data, true)
        ]);
    }

    // Enregistrer un nouveau filleul
    public function registerFilleul(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'civilite' => 'nullable|in:M,Mme',
            'prenom' => 'nullable|string|max:255',
            'nom' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'date_debut_formation' => 'nullable|date',
            'date_inscription' => 'nullable|date',
            'statut' => 'nullable|string',
            'parrain_id' => 'required|exists:users,id',
            'catalogue_formation_id' => 'required|exists:catalogue_formations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Création du user
        $user = User::create([
            'name' => $request->nom,
            'email' => $request->email,
            'password' => bcrypt(Str::random(12)), // Mot de passe temporaire
            'role' => 'stagiaire'
        ]);

        // Création du stagiaire
        $stagiaire = Stagiaire::create([
            'user_id' => $user->id,
            'civilite' => $request->civilite,
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'code_postal' => $request->code_postal,
            'ville' => $request->ville,
            'date_naissance' => $request->date_naissance,
            'date_debut_formation' => $request->date_debut_formation ?? null,
            'date_inscription' => $request->date_inscription ?? now(),
            'statut' => $request->statut ?? 'en_attente',
        ]);

        // Enregistrement du parrainage
        Parrainage::create([
            'parrain_id' => $request->parrain_id,
            'filleul_id' => $user->id,
            'date_parrainage' => now(),
            'points' => 2
        ]);

        DB::table('stagiaire_catalogue_formations')->insert([
            'stagiaire_id' => $stagiaire->id,
            'catalogue_formation_id' => $request->catalogue_formation_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie!',
            'data' => [
                'user' => $user,
                'stagiaire' => $stagiaire
            ]
        ]);
    }

    public function getStatsParrain($parrain_id)
    {
        $nombreFilleuls = Parrainage::where('parrain_id', $parrain_id)->count();
        $totalPoints = Parrainage::where('parrain_id', $parrain_id)->sum('points');

        return response()->json([
            'success' => true,
            'parrain_id' => $parrain_id,
            'nombre_filleuls' => $nombreFilleuls,
            'total_points' => $totalPoints
        ]);
    }
}
