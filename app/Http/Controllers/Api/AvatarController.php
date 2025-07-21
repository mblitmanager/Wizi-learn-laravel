<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Avatar;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AvatarController extends Controller
{
    // GET /api/avatars
    public function index()
    {
        $avatars = Avatar::all();
        return response()->json(['avatars' => $avatars]);
    }

    // GET /api/my-avatars
    public function myAvatars()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $avatars = $user->avatars()->get();
        return response()->json(['avatars' => $avatars]);
    }

    // POST /api/avatars/{id}/unlock
    public function unlock($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $avatar = Avatar::findOrFail($id);
        // Ici, tu peux ajouter la logique de vérification (points, badges, etc.)
        $user->avatars()->syncWithoutDetaching([$avatar->id => ['unlocked_at' => now()]]);
        return response()->json(['success' => true]);
    }
} 