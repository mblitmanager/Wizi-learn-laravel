<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\File;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::all();
        return view('admin.achievements.index', compact('achievements'));
    }

    public function create()
    {
        $quizzes = \App\Models\Quiz::all();
        return view('admin.achievements.create', compact('quizzes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'condition' => 'required|integer',
            'description' => 'required|string',
            'level' => 'nullable|string',
            'quiz_id' => 'nullable|exists:quizzes,id',
            'icon' => 'required|in:tv,handshake,clapper,trophy,party,fire,gold,silver,bronze',
        ]);

        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'condition' => $validated['condition'],
            'description' => $validated['description'],
            'level' => $validated['level'] ?? null,
            'quiz_id' => $validated['quiz_id'] ?? null,
        ];

        // Icône restreinte aux 3 choix
        $data['icon'] = $validated['icon'];

        Achievement::create($data);
        return redirect()->route('admin.achievements.index')->with('success', 'Succès créé avec succès.');
    }

    public function edit(Achievement $achievement)
    {
        $quizzes = \App\Models\Quiz::all();
        return view('admin.achievements.edit', compact('achievement', 'quizzes'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'condition' => 'required|integer',
            'description' => 'required|string',
            'level' => 'nullable|string',
            'quiz_id' => 'nullable|exists:quizzes,id',
            'icon' => 'required|in:tv,handshake,clapper,trophy,party,fire,gold,silver,bronze',
        ]);

        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'condition' => $validated['condition'],
            'description' => $validated['description'],
            'level' => $validated['level'] ?? null,
            'quiz_id' => $validated['quiz_id'] ?? null,
        ];

        // Icône restreinte aux 3 choix
        $data['icon'] = $validated['icon'];

        $achievement->update($data);
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
        try {
            $achievements = Achievement::with('quiz')->get()->map(function ($achievement) {
                return [
                    ...$achievement->toArray(),
                    'quiz_title' => $achievement->quiz ? $achievement->quiz->titre : null,
                ];
            });
            return response()->json(['achievements' => $achievements]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('API Achievements Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    // POST /api/admin/achievements
    public function apiStore(Request $request)
    {
        $achievement = Achievement::create($request->all());

        return response()->json(['achievement' => $achievement], 201);
    }

    /**
     * Affiche les statistiques détaillées des succès par stagiaire et par achievement.
     */
    public function detailedStats()
    {
        $stagiaires = \App\Models\Stagiaire::with(['achievements' => function ($q) {
            $q->withPivot('created_at');
        }])->get();

        $achievements = \App\Models\Achievement::withCount('users')->get();

        return view('admin.achievements.detailed_stats', compact('stagiaires', 'achievements'));
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
        $totalUnlocked = DB::table('stagiaire_achievements')->count();
        $byAchievement = Achievement::withCount('stagiaires')->get();
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
        $totalUnlocked = DB::table('stagiaire_achievements')->count();
        $byAchievement = Achievement::withCount('stagiaires')->get();
        return view('admin.achievements.statistics', compact('totalAchievements', 'totalUnlocked', 'byAchievement'));
    }

    // GET /admin/achievements/trends
    public function trends()
    {
        // Example: count unlocked achievements per day for the last 30 days
        $trends = DB::table('stagiaire_achievements')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        return view('admin.achievements.trends', compact('trends'));
    }
}
