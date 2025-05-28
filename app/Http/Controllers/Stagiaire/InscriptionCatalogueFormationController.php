<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CatalogueFormation;
use App\Models\Stagiaire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InscriptionCatalogueFormation;

class InscriptionCatalogueFormationController extends Controller
{
    public function inscrire(Request $request)
    {
        $request->validate([
            'catalogue_formation_id' => 'required|exists:catalogue_formations,id',
        ]);

        $user = Auth::user();
        $stagiaire = $user->stagiaire;
        if (!$stagiaire) {
            return response()->json(['error' => 'Aucun stagiaire associé à cet utilisateur.'], 403);
        }

        $catalogueFormation = CatalogueFormation::findOrFail($request->catalogue_formation_id);
        // Attacher la formation si pas déjà inscrit
        $stagiaire->catalogue_formations()->syncWithoutDetaching([$catalogueFormation->id]);

        // Envoi du mail au stagiaire
        Mail::to($user->email)->send(new InscriptionCatalogueFormation($stagiaire, $catalogueFormation));

        // Envoi du mail au pôle relation (message personnalisé)
        Mail::to(config('mail.pole_relation_email', 'mblitmanager@gmail.com'))->send(new InscriptionCatalogueFormation($stagiaire, $catalogueFormation, true));

        return response()->json(['success' => true, 'message' => 'Inscription réussie et mails envoyés.']);
    }
}
