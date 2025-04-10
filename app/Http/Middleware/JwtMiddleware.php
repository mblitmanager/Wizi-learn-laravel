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
        ];

        // Vérifier si la route commence par /api/ et n'est pas dans les exceptions
        if (str_starts_with($request->path(), 'api/') && !in_array($request->path(), $except)) {
            try {
                JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Token not valid'], 401);
            }
        }

        return $next($request);
    }
}
