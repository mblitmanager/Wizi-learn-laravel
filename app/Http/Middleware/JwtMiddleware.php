<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        // Routes exemptées de la vérification du token
        $except = [
            'api/login',
            'api/logout',
            'api',
            'api/docs',
            'api/parrainage'
        ];

        if ($request->is('api/media/stream/*')) {
            return $next($request);
        }
        // Vérifier si la route commence par /api/ et n'est pas dans les exceptions
        if (str_starts_with($request->path(), 'api/')) {
            // Si la route commence par api/parrainage/get-data, elle est exemptée
            if (str_starts_with($request->path(), 'api/parrainage/get-data')) {
                return $next($request);
            }

            // Vérifier les autres exceptions
            if (!in_array($request->path(), $except)) {
                try {
                    $user = JWTAuth::parseToken()->authenticate();

                    // Charger la relation stagiaire pour les utilisateurs avec le rôle stagiaire
                    if ($user->role === 'stagiaire') {
                        $user->load('stagiaire');
                    }

                    return $next($request);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Token not valid'], 401);
                }
            }
        }


        return $next($request);
    }
}
