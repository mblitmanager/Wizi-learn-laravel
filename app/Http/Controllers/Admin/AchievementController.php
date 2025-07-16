<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::all();
        return view('admin.achievements.index', compact('achievements'));
    }

    public function create()
    {
        return view('admin.achievements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'condition' => 'required|integer',
            'level' => 'nullable|string',
        ]);
        Achievement::create($request->all());
        return redirect()->route('admin.achievements.index')->with('success', 'Succès créé avec succès.');
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.achievements.edit', compact('achievement'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'condition' => 'required|integer',
            'level' => 'nullable|string',
        ]);
        $achievement->update($request->all());
        return redirect()->route('admin.achievements.index')->with('success', 'Succès mis à jour.');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();
        return redirect()->route('admin.achievements.index')->with('success', 'Succès supprimé.');
    }

    // --- API METHODS ---

    // GET /api/admin/achievements
    public function apiIndex()
    {
        $achievements = Achievement::all();
        return response()->json(['achievements' => $achievements]);
    }

    // POST /api/admin/achievements
    public function apiStore(Request $request)
    {
        $achievement = Achievement::create($request->all());
        return response()->json(['achievement' => $achievement], 201);
    }

    // PUT /api/admin/achievements/{id}
    public function apiUpdate(Request $request, $id)
    {
        $achievement = Achievement::findOrFail($id);
        $achievement->update($request->all());
        return response()->json(['achievement' => $achievement]);
    }

    // DELETE /api/admin/achievements/{id}
    public function apiDestroy($id)
    {
        Achievement::destroy($id);
        return response()->json(['success' => true]);
    }

    // POST /api/admin/achievements/reset
    public function apiResetAchievements(Request $request)
    {
        $stagiaireId = $request->input('stagiaire_id');
        $stagiaire = \App\Models\Stagiaire::findOrFail($stagiaireId);
        $stagiaire->achievements()->detach();
        return response()->json(['success' => true]);
    }

    // GET /api/admin/achievements/statistics
    public function apiStatistics()
    {
        $totalAchievements = Achievement::count();
        $totalUnlocked = \App\Models\UserAchievement::count();
        $byAchievement = Achievement::withCount('users')->get();
        return response()->json([
            'total_achievements' => $totalAchievements,
            'total_unlocked' => $totalUnlocked,
            'by_achievement' => $byAchievement,
        ]);
    }

    // POST /admin/achievements/reset
    public function resetAchievements(Request $request)
    {
        $stagiaireId = $request->input('stagiaire_id');
        $stagiaire = \App\Models\Stagiaire::findOrFail($stagiaireId);
        $stagiaire->achievements()->detach();
        return redirect()->route('admin.achievements.index')->with('success', 'Succès réinitialisés pour le stagiaire.');
    }

    // GET /admin/achievements/statistics
    public function statistics()
    {
        $totalAchievements = Achievement::count();
        $totalUnlocked = \App\Models\UserAchievement::count();
        $byAchievement = Achievement::withCount('users')->get();
        return view('admin.achievements.statistics', compact('totalAchievements', 'totalUnlocked', 'byAchievement'));
    }

    // GET /admin/achievements/trends
    public function trends()
    {
        // Example: count unlocked achievements per day for the last 30 days
        $trends = \App\Models\UserAchievement::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        return view('admin.achievements.trends', compact('trends'));
    }
}
