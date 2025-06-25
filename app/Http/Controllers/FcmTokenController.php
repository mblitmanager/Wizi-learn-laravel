<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    // Enregistre le token FCM pour l'utilisateur connecté
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }
        $user->fcm_token = $request->token;
        $user->save();

        return response()->json(['message' => 'Token enregistré']);
    }
}
