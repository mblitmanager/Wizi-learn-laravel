<?php

/*
 * INSTRUCTIONS: Ajouter ces méthodes dans JWTAuthController.php après la méthode login()
 */

/**
 * Refresh access token using refresh token
 */
public function refresh(Request $request)
{
    $request->validate([
        'refresh_token' => 'required|string',
    ]);

    try {
        $tokens = $this->tokenService->refreshAccessToken($request->refresh_token);
        
        return response()->json($tokens);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'refresh_failed',
            'message' => $e->getMessage()
        ], 401);
    }
}

/**
 * Logout - REMPLACER la méthode logout() existante
 */
public function logout(Request $request)
{
    try {
        $user = auth()->user();
        $token = JWTAuth::getToken();

        if ($user) {
            // Mettre à jour l'historique de connexion
            LoginHistories::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first()
                    ?->update([
                    'logout_at' => now()
                ]);

            // Révoquer le refresh token si fourni
            if ($request->has('refresh_token')) {
                $this->tokenService->revokeToken($request->refresh_token);
            } else {
                // Révoquer tous les tokens de l'utilisateur
                $this->tokenService->revokeAllTokens($user);
            }

            $user->update([
                'is_online' => false,
                'last_activity_at' => now()
            ]);
        }

        // Invalider le JWT
        JWTAuth::invalidate($token);

        return response()->json([
            'message' => 'Déconnexion réussie',
            'logout_at' => now()->toDateTimeString()
        ], 200);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Échec de la déconnexion'], 500);
    }
}
