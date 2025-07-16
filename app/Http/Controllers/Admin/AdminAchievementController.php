<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\Stagiaire;
use Illuminate\Http\Request;

class AdminAchievementController extends Controller
{
    // GET /api/admin/achievements
    public function index()
    {
        $achievements = Achievement::all();
        return response()->json(['achievements' => $achievements]);
    }

    // POST /api/admin/achievements
    public function store(Request $request)
    {
        $achievement = Achievement::create($request->all());
        return response()->json(['achievement' => $achievement], 201);
    }

    // PUT /api/admin/achievements/{id}
    public function update(Request $request, $id)
    {
        $achievement = Achievement::findOrFail($id);
        $achievement->update($request->all());
        return response()->json(['achievement' => $achievement]);
    }

    // DELETE /api/admin/achievements/{id}
    public function destroy($id)
    {
        Achievement::destroy($id);
        return response()->json(['success' => true]);
    }

    // POST /api/admin/achievements/reset
    public function resetAchievements(Request $request)
    {
        $stagiaireId = $request->input('stagiaire_id');
        $stagiaire = Stagiaire::findOrFail($stagiaireId);
        $stagiaire->achievements()->detach();
        return response()->json(['success' => true]);
    }

    // GET /api/admin/achievements/statistics
    public function statistics()
    {
        $totalAchievements = Achievement::count();
        $totalUnlocked = UserAchievement::count();
        $byAchievement = Achievement::withCount('users')->get();
        return response()->json([
            'total_achievements' => $totalAchievements,
            'total_unlocked' => $totalUnlocked,
            'by_achievement' => $byAchievement,
        ]);
    }
}
