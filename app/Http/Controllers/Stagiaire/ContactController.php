<?php

namespace App\Http\Controllers\Stagiaire;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Services\ContactService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Models\Commercial;
use App\Models\Formateur;
use App\Models\PoleRelationClient;

class ContactController extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function getContacts()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }
            if ($user->role != 'formateur' && $user->role != 'admin') {
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }

            // --- Construction manuelle des contacts pour le front ---
            $stagiaireId = $user->stagiaire->id;
            // Formateurs liés au stagiaire
            $formateurs = $user->stagiaire->formateurs()->with(['user', 'catalogue_formations' => function ($q) use ($stagiaireId) {
                $q->whereHas('stagiaires', function ($q2) use ($stagiaireId) {
                    $q2->where('stagiaire_id', $stagiaireId);
                });
            }])->get();

            $formateursArr = $formateurs->map(function ($formateur) use ($stagiaireId) {
                $formations = [];
                // Récupération directe des formations depuis la table pivot
                $pivotFormations = \DB::table('stagiaire_catalogue_formations')
                    ->where('formateur_id', $formateur->id)
                    ->where('stagiaire_id', $stagiaireId)
                    ->get();
                foreach ($pivotFormations as $pivot) {
                    // On récupère la formation associée à l'ID du pivot
                    $formation = \DB::table('catalogue_formations')->where('id', $pivot->catalogue_formation_id)->first();
                    if ($formation) {
                        $formations[] = [
                            'id' => $formation->id,
                            'titre' => $formation->titre,
                            'dateDebut' => $pivot->date_debut ?? null,
                            'dateFin' => $pivot->date_fin ?? null,
                            'formateur' => $formateur->user->name,
                        ];
                    }
                }
                return [
                    'id' => $formateur->id,
                    'type' => 'Formateur',
                    'name' => $formateur->user->name,
                    'email' => $formateur->user->email,
                    'telephone' => $formateur->telephone ?? '',
                    'formations' => $formations,
                ];
            });

            // Commerciaux liés au stagiaire
            $commerciaux = $user->stagiaire->commercials()->with('user')->get()->map(function ($commercial) {
                return [
                    'id' => $commercial->id,
                    'type' => 'Commercial',
                    'name' => $commercial->user->name,
                    'email' => $commercial->user->email,
                    'telephone' => $commercial->telephone ?? '',
                    // 'role' => $commercial->role ?? ($commercial->user->role ?? 'commercial'),
                ];
            });

            // Pôle Relation Client liés au stagiaire
            $poleRelation = $user->stagiaire->poleRelationClients()->with('user')->get()->map(function ($pole) {
                return [
                    'id' => $pole->id,
                    // 'type' => 'Pôle Relation Client',
                    'type' => $pole->role ?? ($pole->user->role ?? 'Pôle Relation Client'),
                    'name' => $pole->user->name,
                    'email' => $pole->user->email,
                    'telephone' => $pole->telephone ?? '',
                    // 'role' => $pole->role ?? ($pole->user->role ?? 'pole relation client'),
                ];
            });

            return response()->json([
                'formateurs' => $formateursArr,
                'commerciaux' => $commerciaux,
                'pole_relation' => $poleRelation,
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        }
    }

    public function getFormateurs()
    {
        try {
            // Si un stagiaire_id est passé en query, on filtre les formations pour ce stagiaire
            $user = JWTAuth::parseToken()->authenticate();
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }
            if ($user->role != 'formateur' && $user->role != 'admin') {
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }

            // --- Construction manuelle des contacts pour le front ---
            $stagiaireId = $user->stagiaire->id;
            // Formateurs liés au stagiaire
            $formateurs = $user->stagiaire->formateurs()->with(['user', 'catalogue_formations' => function ($q) use ($stagiaireId) {
                $q->whereHas('stagiaires', function ($q2) use ($stagiaireId) {
                    $q2->where('stagiaire_id', $stagiaireId);
                });
            }])->get();

            $formateursArr = $formateurs->map(function ($formateur) use ($stagiaireId) {
                $formations = [];
                // Récupération directe des formations depuis la table pivot
                $pivotFormations = \DB::table('stagiaire_catalogue_formations')
                    ->where('formateur_id', $formateur->id)
                    ->where('stagiaire_id', $stagiaireId)
                    ->get();
                foreach ($pivotFormations as $pivot) {
                    // On récupère la formation associée à l'ID du pivot
                    $formation = \DB::table('catalogue_formations')->where('id', $pivot->catalogue_formation_id)->first();
                    if ($formation) {
                        $formations[] = [
                            'id' => $formation->id,
                            'titre' => $formation->titre,
                            'dateDebut' => $pivot->date_debut ?? null,
                            'dateFin' => $pivot->date_fin ?? null,
                            'formateur' => $formateur->user->name,
                        ];
                    }
                }
                return [
                    'id' => $formateur->id,
                    'name' => $formateur->user->name,
                    'email' => $formateur->user->email,
                    'phone' => $formateur->telephone ?? '',
                    'role' => 'Formateur',
                    'formations' => $formations,
                    'avatar' => $formateur->user->image ?? '/images/default-avatar.png',
                    'created_at' => $formateur->created_at->format('d/m/Y')
                ];
            });
            $paginate = PaginationHelper::paginate($formateursArr, 10);
            return response()->json($paginate);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des formateurs'], 500);
        }
    }

    public function getCommerciaux()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }
            if ($user->role != 'formateur' && $user->role != 'admin') {
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }
            // Commerciaux liés au stagiaire
            $commercials = $user->stagiaire->commercials()->with('user')->get()->map(function ($commercial) {
                return [
                    'id' => $commercial->id,
                    'name' => $commercial->user->name,
                    'email' => $commercial->user->email,
                    'phone' => $commercial->telephone ?? '',
                    'role' => 'Commercial',
                    'avatar' => $commercial->user->image ?? '/images/default-avatar.png',
                    'created_at' => $commercial->created_at->format('d/m/Y')
                ];
            });
            $paginate = PaginationHelper::paginate($commercials, 10);
            return response()->json($paginate);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des commerciaux'], 500);
        }
    }

    public function getPoleRelation()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }
            if ($user->role != 'formateur' && $user->role != 'admin') {
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }
            // Pôle Relation Client liés au stagiaire
            $poles = $user->stagiaire->poleRelationClients()->with('user')->get()->map(function ($pole) {
                return [
                    'id' => $pole->id,
                    'name' => $pole->user->name,
                    'email' => $pole->user->email,
                    'phone' => $pole->telephone ?? '',
                    'role' => 'Pôle Relation',
                    'avatar' => $pole->user->image ?? '/images/default-avatar.png',
                    'created_at' => $pole->created_at->format('d/m/Y')
                ];
            });
            $paginate = PaginationHelper::paginate($poles, 10);
            return response()->json($paginate);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération du pôle relation'], 500);
        }
    }



    public function addContact(Request $request)
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
            $contact = $this->contactService->addContact($user->stagiaire->id, $request->all());
            return response()->json($contact, 201);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        }
    }
}
