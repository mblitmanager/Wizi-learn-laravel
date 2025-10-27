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
use Illuminate\Support\Facades\DB;

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
                $pivotFormations = DB::table('stagiaire_catalogue_formations')
                    ->where('formateur_id', $formateur->id)
                    ->where('stagiaire_id', $stagiaireId)
                    ->get();
                foreach ($pivotFormations as $pivot) {
                    // On récupère la formation associée à l'ID du pivot
                    $formation = DB::table('catalogue_formations')->where('id', $pivot->catalogue_formation_id)->first();
                    if ($formation) {
                        $formations[] = [
                            'id' => $formation->id,
                            'titre' => $formation->titre,
                            'dateDebut' => $pivot->date_debut ?? null,
                            'dateFin' => $pivot->date_fin ?? null,
                            'formateur' => $formateur->user->name,
                            'image' => $formation->user->image ?? '/images/default-formation.png',
                        ];
                    }
                }
                // Prefer prenom on the relation model (Formateur) then fall back to user fields or split name
                $user = $formateur->user;
                $prenom = $formateur->prenom ?? $user->prenom ?? null;
                $nom = $formateur->nom ?? $user->nom ?? null;
                $civilite = $formateur->civilite ?? $user->civilite ?? null;

                if (empty($prenom) && empty($nom) && !empty($user->name)) {
                    $parts = preg_split('/\s+/', trim($user->name));
                    if (count($parts) > 1) {
                        // If the relation doesn't have prenom/nom, split the user's name
                        $prenom = array_shift($parts);
                        $nom = implode(' ', $parts);
                    } else {
                        // single word name -> treat as nom
                        $nom = $user->name;
                        $prenom = '';
                    }
                }

                return [
                    'id' => $formateur->id,
                    'type' => 'Formateur',
                    'name' => $formateur->user->name,
                    'prenom' => $prenom ?? '',
                    'nom' => $nom ?? '',
                    'civilite' => $civilite,
                    'role' => $formateur->role ?? 'Formateur',
                    'email' => $formateur->user->email,
                    'telephone' => $formateur->telephone ?? '',
                    'formations' => $formations,
                    'image' => $formateur->user->image ?? '/images/default-avatar.png',
                ];
            });

            // Commerciaux liés au stagiaire
            $commerciaux = $user->stagiaire->commercials()->with('user')->get()->map(function ($commercial) {
                // Prefer prenom on Commercial model then user's fields
                $user = $commercial->user;
                $prenom = $commercial->prenom ?? $user->prenom ?? null;
                $nom = $commercial->nom ?? $user->nom ?? null;
                $civilite = $commercial->civilite ?? $user->civilite ?? null;

                if (empty($prenom) && empty($nom) && !empty($user->name)) {
                    $parts = preg_split('/\s+/', trim($user->name));
                    if (count($parts) > 1) {
                        $prenom = array_shift($parts);
                        $nom = implode(' ', $parts);
                    } else {
                        $nom = $user->name;
                        $prenom = '';
                    }
                }
                return [
                    'id' => $commercial->id,
                    'type' => 'Commercial',
                    'name' => $commercial->user->name,
                    'prenom' => $prenom ?? '',
                    'nom' => $nom ?? '',
                    'civilite' => $civilite,
                    'email' => $commercial->user->email,
                    'role' => $commercial->role ?? 'Commercial',
                    'telephone' => $commercial->telephone ?? '',
                    'image' => $commercial->user->image ?? '/images/default-avatar.png',
                ];
            });

            // Pôle Relation Client - Filtrer par rôle
            $poleRelation = $user->stagiaire->poleRelationClients()
                ->with('user')
                ->whereNotIn('role', [
                    'Pôle SAV',
                    'Chargée Administration des Ventes',
                    'Responsable suivi formation & SAV & Parrainage'
                ])
                ->get()
                ->map(function ($pole) {
                    $user = $pole->user;
                    $civilite = $pole->civilite ?? $user->civilite ?? null;

                    return [
                        'id' => $pole->id,
                        'type' => $pole->role ?? 'pole_relation_client',
                        'name' => $pole->user->name,
                        'prenom' => $prenom ?? '',
                        'civilite' => $civilite,
                        'email' => $pole->user->email,
                        'role' => $pole->role ?? 'Pôle Relation Client',
                        'telephone' => $pole->telephone ?? '',
                        'image' => $pole->user->image ?? '/images/default-avatar.png',
                    ];
                });

            // Pôle SAV - Filtrer par rôle dans PoleRelationClient
            $poleSav = $user->stagiaire->poleRelationClients()
                ->with('user')
                ->whereIn('role', [
                    'Pôle SAV',
                    'Chargée Administration des Ventes',
                    'Responsable suivi formation & SAV & Parrainage'
                ])
                ->get()
                ->map(function ($sav) {
                    $user = $sav->user;
                    $civilite = $sav->civilite ?? $user->civilite ?? null;

                    return [
                        'id' => $sav->id,
                        'type' => 'pole_sav',
                        'name' => $sav->user->name,
                        'prenom' => $prenom ?? '',
                        'civilite' => $civilite,
                        'email' => $sav->user->email,
                        'telephone' => $sav->telephone ?? '',
                        'role' => $sav->role ?? 'Pôle SAV',
                        'image' => $sav->user->image ?? '/images/default-avatar.png',
                    ];
                });

            return response()->json([
                'formateurs' => $formateursArr,
                'commerciaux' => $commerciaux,
                'pole_relation' => $poleRelation,
                'pole_sav' => $poleSav,
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        }
    }

    public function getFormateurs()
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

            $stagiaireId = $user->stagiaire->id;
            $formateurs = $user->stagiaire->formateurs()->with(['user', 'catalogue_formations' => function ($q) use ($stagiaireId) {
                $q->whereHas('stagiaires', function ($q2) use ($stagiaireId) {
                    $q2->where('stagiaire_id', $stagiaireId);
                });
            }])->get();

            $formateursArr = $formateurs->map(function ($formateur) use ($stagiaireId) {
                $formations = [];
                $pivotFormations = DB::table('stagiaire_catalogue_formations')
                    ->where('formateur_id', $formateur->id)
                    ->where('stagiaire_id', $stagiaireId)
                    ->get();
                foreach ($pivotFormations as $pivot) {
                    $formation = DB::table('catalogue_formations')->where('id', $pivot->catalogue_formation_id)->first();
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
                $user = $formateur->user;
                $prenom = $user->prenom ?? null;
                $nom = $user->nom ?? null;
                $civilite = $formateur->civilite ?? $user->civilite ?? null;

                if (empty($prenom) && empty($nom) && !empty($user->name)) {
                    $parts = preg_split('/\s+/', trim($user->name));
                    if (count($parts) > 1) {
                        $prenom = array_shift($parts);
                        $nom = implode(' ', $parts);
                    } else {
                        $nom = $user->name;
                        $prenom = '';
                    }
                }
                return [
                    'id' => $formateur->id,
                    'name' => $formateur->user->name,
                    'prenom' => $prenom ?? '',
                    'nom' => $nom ?? '',
                    'civilite' => $civilite,
                    'email' => $formateur->user->email,
                    'phone' => $formateur->telephone ?? '',
                    'role' => $formateur->role ?? 'Formateur',
                    'type' => 'Formateur',
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

            $commercials = $user->stagiaire->commercials()->with('user')->get()->map(function ($commercial) {
                $user = $commercial->user;
                $prenom = $user->prenom ?? null;
                $nom = $user->nom ?? null;
                $civilite = $commercial->civilite ?? $user->civilite ?? null;

                if (empty($prenom) && empty($nom) && !empty($user->name)) {
                    $parts = preg_split('/\s+/', trim($user->name));
                    if (count($parts) > 1) {
                        $prenom = array_shift($parts);
                        $nom = implode(' ', $parts);
                    } else {
                        $nom = $user->name;
                        $prenom = '';
                    }
                }
                return [
                    'id' => $commercial->id,
                    'name' => $commercial->user->name,
                    'prenom' => $prenom ?? '',
                    'nom' => $nom ?? '',
                    'civilite' => $civilite,
                    'email' => $commercial->user->email,
                    'phone' => $commercial->telephone ?? '',
                    'role' => $commercial->role ?? 'Commercial',
                    'type' => 'Commercial',
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

            $poles = $user->stagiaire->poleRelationClients()->with('user')->get()->map(function ($pole) {
                $user = $pole->user;
                $prenom = $user->prenom ?? null;
                $nom = $user->nom ?? null;
                $civilite = $pole->civilite ?? $user->civilite ?? null;

                if (empty($prenom) && empty($nom) && !empty($user->name)) {
                    $parts = preg_split('/\s+/', trim($user->name));
                    if (count($parts) > 1) {
                        $prenom = array_shift($parts);
                        $nom = implode(' ', $parts);
                    } else {
                        $nom = $user->name;
                        $prenom = '';
                    }
                }
                return [
                    'id' => $pole->id,
                    'name' => $pole->user->name,
                    'prenom' => $prenom ?? '',
                    'nom' => $nom ?? '',
                    'civilite' => $civilite,
                    'email' => $pole->user->email,
                    'phone' => $pole->telephone ?? '',
                    'role' => $pole->role ?? 'Pôle Relation Client',
                    'type' => 'pole_relation_client',
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

    public function getPoleSav()
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

            $poleSav = $user->stagiaire->poleRelationClients()
                ->with('user')
                ->whereIn('role', [
                    'Pôle SAV',
                    'Chargée Administration des Ventes',
                    'Responsable suivi formation & SAV & Parrainage'
                ])
                ->get()
                ->map(function ($sav) {
                    $user = $sav->user;
                    $civilite = $sav->civilite ?? $user->civilite ?? null;

                    return [
                        'id' => $sav->id,
                        'name' => $sav->user->name,
                        'civilite' => $civilite,
                        'email' => $sav->user->email,
                        'phone' => $sav->telephone ?? '',
                        'role' => $sav->role ?? 'Pôle SAV',
                        'type' => 'pole_sav',
                        'avatar' => $sav->user->image ?? '/images/default-avatar.png',
                        'created_at' => $sav->created_at->format('d/m/Y')
                    ];
                });

            $paginate = PaginationHelper::paginate($poleSav, 10);
            return response()->json($paginate);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération du pôle SAV'], 500);
        }
    }

    public function addContact(Request $request)
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
            $contact = $this->contactService->addContact($user->stagiaire->id, $request->all());
            return response()->json($contact, 201);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        }
    }
}
