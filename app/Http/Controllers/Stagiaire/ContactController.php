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
            $contacts = $this->contactService->getContactsByStagiaire($user->id);
            return response()->json($contacts);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getFormateurs()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $formateurs = $this->contactService->getFormateurContacts($user->id);
            return response()->json($formateurs);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getCommerciaux()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $commerciaux = $this->contactService->getCommercialContacts($user->id);
            return response()->json($commerciaux);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getPoleRelation()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $poleRelation = $this->contactService->getPoleRelationContacts($user->id);
            return response()->json($poleRelation);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function addContact(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $contact = $this->contactService->addContact($user->id, $request->all());
            return response()->json($contact, 201);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
} 