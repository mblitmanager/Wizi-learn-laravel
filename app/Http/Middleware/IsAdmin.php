<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'administrateur') {
            return $next($request);
        }

        // Rediriger vers la page de connexion si non authentifié
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Rediriger vers le dashboard formateur si c'est un formateur
        if (Auth::user()->role === 'formateur') {
            return redirect()->route('formateur.dashboard');
        }

        return redirect()->route('dashboard')->with('error', 'Access denied.');
    }
}
