<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            // Mettre à jour seulement si la dernière activité date de plus de 5 minutes
            if (auth()->user()->last_activity_at < now()->subMinutes(5)) {
                auth()->user()->update([
                    'last_activity_at' => now(),
                    'is_online' => true
                ]);
            }
        }

        return $next($request);
    }
}
