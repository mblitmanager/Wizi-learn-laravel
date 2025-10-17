<?php
// app/Http/Middleware/IsFormateur.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsFormateur
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'formateur' || Auth::user()->role === 'formatrice') {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Access denied.');
    }
}
