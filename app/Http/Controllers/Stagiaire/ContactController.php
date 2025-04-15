<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\ContactService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

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
            $formateurs = $this->contactService->getFormateurContacts($user->stagiaire->id);
            return response()->json($formateurs);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
        }
    }

    public function getCommerciaux()
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
            $commerciaux = $this->contactService->getCommercialContacts($user->stagiaire->id);
            return response()->json($commerciaux);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getPoleRelation()
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
            $poleRelation = $this->contactService->getPoleRelationContacts($user->stagiaire->id);
            return response()->json($poleRelation);
        } catch (JWTException $e) {
            return response()->json(['error' => 'non autorisé'], 401);
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