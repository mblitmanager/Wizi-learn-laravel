<?php

namespace App\Http\Controllers\Stagiaire;

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

            $contacts = $this->contactService->getContactsByStagiaire($user->stagiaire->id);
            return response()->json($contacts);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        }
    }

    public function getFormateurs()
    {
        try {
            $formateurs = Formateur::with(['user', 'formations'])
                ->get()
                ->map(function ($formateur) {
                    return [
                        'id' => $formateur->id,
                        'name' => $formateur->user->name,
                        'email' => $formateur->user->email,
                        'phone' => $formateur->telephone ?? '',
                        'role' => 'Formateur',
                        'formations' => $formateur->formations->pluck('titre')->toArray(),
                        'avatar' => $formateur->user->avatar ?? '/images/default-avatar.png',
                        'created_at' => $formateur->created_at->format('d/m/Y')
                    ];
                });

            return response()->json($formateurs);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des formateurs'], 500);
        }
    }

    public function getCommerciaux()
    {
        try {
            $commercials = Commercial::with(['user'])
            ->get()
            ->map(function ($commercial) {
                return [
                    'id' => $commercial->id,
                    'name' => $commercial->user->name,
                    'email' => $commercial->user->email,
                    'phone' => $commercial->telephone ?? '',
                    'role' => 'Commercial',
                    'avatar' => $commercial->user->avatar ?? '/images/default-avatar.png',
                    'created_at' => $commercial->created_at->format('d/m/Y')
                ];
            });

        return response()->json($commercials);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des commerciaux'], 500);
        }
    }

    public function getPoleRelation()
    {
        try {
            $poles   = PoleRelationClient::with(['user'])
            ->get()
            ->map(function ($pole) {
                return [
                    'id' => $pole->id,
                    'name' => $pole->user->name,
                    'email' => $pole->user->email,
                    'phone' => $pole->telephone ?? '',
                    'role' => 'Pôle Relation',
                    'avatar' => $pole->user->avatar ?? '/images/default-avatar.png',
                    'created_at' => $pole->created_at->format('d/m/Y')
                ];
            });

        return response()->json($poles);
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
