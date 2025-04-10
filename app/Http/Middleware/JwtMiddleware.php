<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        $except = [
            'api/login',
            'api/user',
            'api/register',
            'api/docs',
            'login',
            'register',
            'stagiaires',
            'dashboard',
        ];

        if (in_array($request->path(), $except)) {
            return $next($request); // Bypass JWT check
        }

        try {
            JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token not valid'], 401);
        }

        return $next($request);
    }

}
