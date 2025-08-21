<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function getMyPartner(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $stagiaire = $user->stagiaire;
        if (!$stagiaire) {
            return response()->json(['message' => 'Stagiaire introuvable pour cet utilisateur'], 404);
        }

        // Priorité: relation directe partenaire_id
        $partenaire = $stagiaire->partenaire;

        // Fallback: via la table pivot partenaire_stagiaire s'il n'y a pas de partenaire_id
        if (!$partenaire) {
            $partenaire = Partenaire::whereHas('stagiaires', function ($q) use ($stagiaire) {
                $q->where('stagiaire_id', $stagiaire->id);
            })->first();
        }

        if (!$partenaire) {
            return response()->json(['message' => 'Aucun partenaire associé'], 404);
        }

        return response()->json([
            'identifiant' => $partenaire->identifiant,
            'type' => $partenaire->type,
            'adresse' => $partenaire->adresse,
            'ville' => $partenaire->ville,
            'departement' => $partenaire->departement,
            'code_postal' => $partenaire->code_postal,
            'logo' => $partenaire->logo,
            'actif' => (bool) ($partenaire->actif ?? true),
            'contacts' => $partenaire->contacts ?? [],
        ]);
    }
}
