<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MissionController extends Controller
{
    // GET /api/missions
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $stagiaire = $user->stagiaire;
        if (!$stagiaire) return response()->json(['error' => 'Non autorisé'], 403);
        $missions = Mission::where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();
        $missions = $missions->map(function($mission) use ($stagiaire) {
            $pivot = $mission->stagiaires()->where('stagiaire_id', $stagiaire->id)->first()?->pivot;
            return [
                'id' => $mission->id,
                'title' => $mission->title,
                'description' => $mission->description,
                'type' => $mission->type,
                'goal' => $mission->goal,
                'reward' => $mission->reward,
                'progress' => $pivot?->progress ?? 0,
                'completed' => $pivot?->completed ?? false,
                'completed_at' => $pivot?->completed_at,
            ];
        });
        return response()->json(['missions' => $missions]);
    }

    // POST /api/missions/{id}/progress
    public function updateProgress($id, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $stagiaire = $user->stagiaire;
        if (!$stagiaire) return response()->json(['error' => 'Non autorisé'], 403);
        $mission = Mission::findOrFail($id);
        $progress = $request->input('progress');
        $stagiaire->missions()->syncWithoutDetaching([
            $mission->id => ['progress' => $progress]
        ]);
        return response()->json(['success' => true]);
    }

    // POST /api/missions/{id}/complete
    public function complete($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $stagiaire = $user->stagiaire;
        if (!$stagiaire) return response()->json(['error' => 'Non autorisé'], 403);
        $mission = Mission::findOrFail($id);
        $stagiaire->missions()->syncWithoutDetaching([
            $mission->id => ['completed' => true, 'completed_at' => now()]
        ]);
        return response()->json(['success' => true]);
    }
} 