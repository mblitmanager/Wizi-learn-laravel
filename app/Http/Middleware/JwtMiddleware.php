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
        $exemptRoutes = [
            'api/login',
            'api/logout',
            'api',
            'api/docs',
            'api/parrainage/generate-link',
            'api/parrainage/get-data/*', // Utilisation du wildcard pour toutes les URLs sous ce path
            'api/parrainage/register-filleul',
            'api/formationParrainage',
            'api/events',
            'api/events/test-notification',
            'api/events/listen',
            'api/events/create',
        ];

        // Vérifier si la route actuelle correspond à une route exemptée
        foreach ($exemptRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        // Traitement spécial pour le streaming média
        if ($request->is('api/media/stream/*')) {
            return $next($request);
        }

        // Appliquer le JWT pour toutes les autres routes API
        if (str_starts_with($request->path(), 'api/')) {
            try {
                $user = JWTAuth::parseToken()->authenticate();

                if ($user->role === 'stagiaire') {
                    $user->load('stagiaire');
                }

                return $next($request);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Token not valid'], 401);
            }
        }

        return $next($request);
    }
}
