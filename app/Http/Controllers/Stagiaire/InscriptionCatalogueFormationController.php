<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CatalogueFormation;
use App\Models\Stagiaire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InscriptionCatalogueFormation;
use App\Models\DemandeInscription;
use App\Services\NotificationService;

class InscriptionCatalogueFormationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function inscrire(Request $request)
    {
        try {
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

            // Enregistrement de la demande d'inscription
            DemandeInscription::create([
                'formation_id' => $catalogueFormation->id,
                'filleul_id' => $user->id, // L'utilisateur qui s'inscrit
                'parrain_id' => null, // Pas de parrain pour une inscription directe
                'motif' => 'Demande d\'inscription à une formation',
                'statut' => 'en_attente',
                'date_demande' => now(),
                'date_inscription' => now(),
                'donnees_formulaire' => json_encode([
                    'type' => 'inscription_directe',
                    'formation_id' => $catalogueFormation->id,
                    'date' => now()->toDateTimeString(),
                    'user_id' => $user->id,
                ]),
                'lien_parrainage' => null,
            ]);

            // Envoi du mail au stagiaire
            Mail::to($user->email)->send(new InscriptionCatalogueFormation($stagiaire, $catalogueFormation));

            // Envoi du mail aux adresses fixes
            $notificationEmails = [
                'adv@aopia.fr',
                'alexandre.florek@aopia.fr',
                'mbl.service.mada2@gmail.com'
            ];

            foreach ($notificationEmails as $email) {
                Mail::to($email)->send(new InscriptionCatalogueFormation($stagiaire, $catalogueFormation, true));
            }

            // Notification pour le stagiaire
            $this->notificationService->notifyCustom(
                $user->id,
                'inscription',
                "Nous avons bien reçu votre demande d'inscription, votre conseiller/conseillère va prendre contact avec vous."
            );

            return response()->json([
                'success' => true,
                'message' => 'Un mail de confirmation vous a été envoyé, votre conseiller va bientôt prendre contact avec vous.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de l\'inscription.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
