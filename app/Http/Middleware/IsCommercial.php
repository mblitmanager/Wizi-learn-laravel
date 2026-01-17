<?php
// app/Http/Middleware/IsCommercial.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsCommercial
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Auth::user()->role, ['commercial', 'commerciale'])) {
            return $next($request);
        }

        return response()->json([
            'error' => 'Accès refusé - Rôle commercial requis',
            'status' => 403
        ], 403);
    }
}
