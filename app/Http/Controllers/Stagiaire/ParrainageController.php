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
use App\Mail\CommercialFilleulInscriptionNotification;
use App\Mail\FilleulInscriptionConfirmation;
use App\Models\CatalogueFormation;
use App\Models\DemandeInscription;
use App\Models\DemandeInscriptionParrainage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'date_debut_formation' => 'nullable|date',
            'date_inscription' => 'nullable|date',
            'statut' => 'nullable|string',
            'parrain_id' => 'required|exists:users,id',
            'catalogue_formation_id' => 'nullable|exists:catalogue_formations,id', // Rendre nullable
            'lien_parrainage' => 'nullable|string',
            'motif' => 'required',
        ], [
            'parrain_id.required' => 'Le parrain est requis',
            'telephone.required' => 'Le numéro de téléphone est requis',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'email.email' => 'Veuillez fournir une adresse e-mail valide.',
            'motif.required' => 'Le motif est requis',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Générer un email temporaire si non fourni
            $email = $request->email;
            if (empty($email)) {
                $email = 'temp_' . Str::random(10) . '@parrainage.com';
            }

            // Création du user
            $user = User::create([
                'name' => $request->nom ?? 'Filleul ' . Str::random(5),
                'email' => $email,
                'password' => bcrypt(Str::random(12)),
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
            $parrainage = Parrainage::create([
                'parrain_id' => $request->parrain_id,
                'filleul_id' => $user->id,
                'date_parrainage' => now(),
                'points' => 2,
                'gains' => 50.00
            ]);

            // Association avec la formation seulement si fournie
            if ($request->catalogue_formation_id) {
                DB::table('stagiaire_catalogue_formations')->insert([
                    'stagiaire_id' => $stagiaire->id,
                    'catalogue_formation_id' => $request->catalogue_formation_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Enregistrement de la demande de parrainage
            DemandeInscription::create([
                'parrain_id' => $request->parrain_id,
                'filleul_id' => $user->id,
                'formation_id' => $request->catalogue_formation_id,
                'statut' => 'complete',
                'donnees_formulaire' => json_encode($request->all()),
                'lien_parrainage' => $request->lien_parrainage,
                'motif' => $request->motif,
                'date_demande' => now(),
                'date_inscription' => now(),
            ]);

            $parrain = User::with('stagiaire.commercial')->find($request->parrain_id);
            $formation = $request->catalogue_formation_id ? CatalogueFormation::find($request->catalogue_formation_id) : null;

            // Envoyer une notification au parrain
            if ($parrain) {
                app(\App\Services\NotificationService::class)->sendFcmToUser(
                    $parrain,
                    'Nouveau filleul',
                    'Vous avez reçu un nouveau filleul !',
                    ['type' => 'parrainage', 'filleul_id' => $user->id]
                );

                // Récupérer le commercial associé au parrain
                $commerciaux = $parrain->stagiaire->commercial()->with('user')->get();

                // Envoyer l'email au commercial avec les informations du filleul
                if ($commerciaux->isNotEmpty()) {
                    foreach ($commerciaux as $commercial) {
                        if ($commercial->user?->email) {
                            try {
                                if (view()->exists('emails.commercial_inscription')) {
                                    Mail::to($commercial->user->email)->send(
                                        new CommercialFilleulInscriptionNotification(
                                            $user,
                                            $parrain,
                                            $commercial,
                                            $request->all() // Toutes les données du formulaire
                                        )
                                    );
                                } else {
                                    Log::error('Email template commercial_inscription not found');
                                }
                            } catch (\Exception $e) {
                                Log::error('Failed to send email to commercial: ' . $e->getMessage());
                                continue;
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie! Le commercial a été notifié.',
                'data' => [
                    'user' => $user,
                    'stagiaire' => $stagiaire,
                    'parrainage' => $parrainage
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erreur lors de l'inscription du filleul: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getStatsParrain(Request $request)
    {
        $parrain_id = $request->user()->id;
        $nombreFilleuls = Parrainage::where('parrain_id', $parrain_id)->count();
        $totalPoints = Parrainage::where('parrain_id', $parrain_id)->sum('points');

        return response()->json([
            'success' => true,
            'parrain_id' => $parrain_id,
            'nombre_filleuls' => $nombreFilleuls,
            'total_points' => $totalPoints,
            'gains' => Parrainage::where('parrain_id', $parrain_id)->sum('gains'),
        ]);
    }
}
