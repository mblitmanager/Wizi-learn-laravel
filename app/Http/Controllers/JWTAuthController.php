<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "JWT Authentication API",
    version: "1.0.0",
    description: "API documentation for JWT-based authentication"
)]
class JWTAuthController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "Enregistrer un utilisateur",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Mathieu Dupont"),
                    new OA\Property(property: "email", type: "string", example: "mathieu.dupont@example.com"),
                    new OA\Property(property: "password", type: "string", example: "PassWord123"),
                    new OA\Property(property: "password_confirmation", type: "string", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Utilisateur enregistré avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", type: "object"),
                        new OA\Property(property: "token", type: "string"),
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Erreur de validation"),
        ]
    )]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);
        if ($user = auth()->user()) {
            $user->update([
                'last_activity_at' => now()
            ]);
        }

        return response()->json(compact('user', 'token'), 201);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Connexion de l'utilisateur",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "john.doe@example.com"),
                    new OA\Property(property: "password", type: "string", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Connexion réussie",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Accès invalide"),
            new OA\Response(response: 500, description: "Erreur interne du serveur"),
        ]
    )]
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Accès invalide'], 401);
            }

            // Récupérer l'IP client (avec prise en compte des proxies)
            $ip = $request->header('X-Forwarded-For') ?? $request->ip();
            // Mettre à jour les informations de connexion
            $user = auth()->user();
            $user->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'last_login_ip' => $ip,
                'is_online' => true
            ]);

            // Ajouter le rôle dans le token JWT
            $token = JWTAuth::claims([
                'role' => $user->role,
                'ip' => $ip // Optionnel: stocker l'IP dans le token
            ])->fromUser($user);

            return response()->json([
                'token' => $token,
                'user' => $user->load('stagiaire') // Charger la relation stagiaire si nécessaire
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Impossible de créer le token'], 500);
        }
    }

    #[OA\Get(
        path: "/api/user",
        summary: "Recuperer le profil de l'utilisateur",
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Données de l'utilisateur récupérées avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", type: "object"),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Utilisateur non trouvé"),
            new OA\Response(response: 400, description: "Token invalide"),
        ]
    )]
    public function getUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token invalide'], 400);
        }

        return response()->json(compact('user'));
    }

    #[OA\Get(
        path: "/api/me",
        summary: "Récupérer les informations complètes de l'utilisateur connecté",
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Données de l'utilisateur et du stagiaire récupérées avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", type: "object"),
                        new OA\Property(property: "stagiaire", type: "object"),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Utilisateur non trouvé"),
            new OA\Response(response: 400, description: "Token invalide"),
        ]
    )]
    public function getMe()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 404);
            }

            // Récupérer les informations du stagiaire associé
            $stagiaire = \App\Models\Stagiaire::where('user_id', $user->id)->first();

            return response()->json([
                'user' => $user,
                'stagiaire' => $stagiaire
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token invalide'], 400);
        }
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Déconnexion de l'utilisateur",
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Déconnexion réussie",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Deconnexion réussie"),
                    ]
                )
            ),
        ]
    )]
    public function logout()
    {
        try {
            // Mettre à jour le statut avant la déconnexion
            if ($user = auth()->user()) {
                $user->update([
                    'is_online' => false,
                    'last_activity_at' => now()
                ]);
            }

            // Invalider le token JWT
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Déconnexion réussie',
                'logout_at' => now()->toDateTimeString()
            ], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Échec de la déconnexion'], 500);
        }
    }
}
