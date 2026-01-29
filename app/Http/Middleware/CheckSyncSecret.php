<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSyncSecret
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = $request->header('x-sync-secret');
        if ($secret !== env('SYNC_API_SECRET')) {
            return response()->json(['error' => 'Unauthorized: Invalid sync secret'], 401);
        }
        return $next($request);
    }
}
