<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\LoginHistories;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use League\ISO3166\ISO3166;
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

    protected function getBrowser()
    {
        $agent = new Agent();
        return $agent->browser();
    }

    protected function getPlatform()
    {
        $agent = new Agent();
        return $agent->platform();
    }
//
//    #[OA\Post(
//        path: "/api/login",
//        summary: "Connexion de l'utilisateur",
//        tags: ["Authentication"],
//        requestBody: new OA\RequestBody(
//            required: true,
//            content: new OA\JsonContent(
//                required: ["email", "password"],
//                properties: [
//                    new OA\Property(property: "email", type: "string", example: "john.doe@example.com"),
//                    new OA\Property(property: "password", type: "string", example: "password123"),
//                ]
//            )
//        ),
//        responses: [
//            new OA\Response(
//                response: 200,
//                description: "Connexion réussie",
//                content: new OA\JsonContent(
//                    properties: [
//                        new OA\Property(property: "token", type: "string"),
//                    ]
//                )
//            ),
//            new OA\Response(response: 401, description: "Accès invalide"),
//            new OA\Response(response: 500, description: "Erreur interne du serveur"),
//        ]
//    )]
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Accès invalide'], 401);
            }

            $ip = $request->header('X-Client-IP') ?? $request->ip();
            $user = auth()->user();
            $location = $this->getLocation($ip);

            // Mise à jour utilisateur
            $user->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'last_login_ip' => $ip,
                'is_online' => true
            ]);
            $location = $this->getLocation($ip);
            $countryCode = $location['country'] ?? null;
            $countryName = $countryCode ? (new ISO3166())->alpha2($countryCode)['name'] : null;
            // Enregistrement historique
            LoginHistories::create([
                'user_id' => $user->id,
                'ip_address' => $ip,
                'country' => $countryName,
                'city' => $location['city'] ?? null,
                'device' => $request->userAgent(),
                'login_at' => now(),
                'browser' => $this->getBrowser(),
                'platform' => $this->getPlatform(),
                'login_at' => now()
            ]);

            $token = JWTAuth::claims([
                'role' => $user->role,
                'ip' => $ip
            ])->fromUser($user);

            return response()->json([
                'token' => $token,
                'user' => $user->load('stagiaire')
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Impossible de créer le token'], 500);
        }
    }
    private function getLocation($ip)
    {
        if ($ip === '127.0.0.1') return [];

        try {
            $response = Http::get("https://ipinfo.io/{$ip}/json?token=" . config('services.ipinfo.token'));
            return $response->json();
        } catch (\Exception $e) {
            Log::error("IP location failed: " . $e->getMessage());
            return [];
        }
    }
//    #[OA\Get(
//        path: "/api/user",
//        summary: "Recuperer le profil de l'utilisateur",
//        tags: ["Authentication"],
//        responses: [
//            new OA\Response(
//                response: 200,
//                description: "Données de l'utilisateur récupérées avec succès",
//                content: new OA\JsonContent(
//                    properties: [
//                        new OA\Property(property: "user", type: "object"),
//                    ]
//                )
//            ),
//            new OA\Response(response: 404, description: "Utilisateur non trouvé"),
//            new OA\Response(response: 400, description: "Token invalide"),
//        ]
//    )]
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

//    #[OA\Get(
//        path: "/api/me",
//        summary: "Récupérer les informations complètes de l'utilisateur connecté",
//        tags: ["Authentication"],
//        responses: [
//            new OA\Response(
//                response: 200,
//                description: "Données de l'utilisateur et du stagiaire récupérées avec succès",
//                content: new OA\JsonContent(
//                    properties: [
//                        new OA\Property(property: "user", type: "object"),
//                        new OA\Property(property: "stagiaire", type: "object"),
//                    ]
//                )
//            ),
//            new OA\Response(response: 404, description: "Utilisateur non trouvé"),
//            new OA\Response(response: 400, description: "Token invalide"),
//        ]
//    )]
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
//
//    #[OA\Post(
//        path: "/api/logout",
//        summary: "Déconnexion de l'utilisateur",
//        tags: ["Authentication"],
//        responses: [
//            new OA\Response(
//                response: 200,
//                description: "Déconnexion réussie",
//                content: new OA\JsonContent(
//                    properties: [
//                        new OA\Property(property: "message", type: "string", example: "Deconnexion réussie"),
//                    ]
//                )
//            ),
//        ]
//    )]
    public function logout()
    {
        try {
            $user = auth()->user();
            $token = JWTAuth::getToken();

            if ($user) {
                // Mettre à jour le dernier historique de connexion
                LoginHistories::where('user_id', $user->id)
                    ->whereNull('logout_at')
                    ->latest()
                    ->first()
                    ?->update([
                        'logout_at' => now()
                    ]);

                $user->update([
                    'is_online' => false,
                    'last_activity_at' => now()
                ]);
            }

            JWTAuth::invalidate($token);

            return response()->json([
                'message' => 'Déconnexion réussie',
                'logout_at' => now()->toDateTimeString()
            ], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Échec de la déconnexion'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Demande de réinitialisation de mot de passe",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lien envoyé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Un lien de réinitialisation a été envoyé à votre adresse email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Email non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Email non trouvé")
     *         )
     *     )
     * )
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'reset_url' => 'required|url' // URL de base du frontend
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email non trouvé'], 404);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetLink = $request->reset_url . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        Mail::to($user->email)->send(new PasswordResetMail($resetLink));
        return response()->json(['message' => 'Email envoyé']);

    }
    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Réinitialisation du mot de passe",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="reset_token_123"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mot de passe réinitialisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Mot de passe réinitialisé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Token invalide ou expiré",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Token invalide ou expiré")
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json(['error' => 'Token invalide ou expiré'], 400);
        }

        if (now()->subMinutes(60)->gt($record->created_at)) {
            return response()->json(['error' => 'Token expiré'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès']);
    }
}
