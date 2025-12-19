<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Stagiaire;
use App\Models\Notification;
use App\Models\QuizParticipation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoReminderController extends Controller
{
    /**
     * Get statistics for auto-reminders monitoring
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        $isFormateur = $user->role === 'formateur' || $user->role === 'formatrice';
        $isCommercial = $user->role === 'commercial' || $user->role === 'commerciale';
        
        $stagiaireIds = null;
        if ($isFormateur) {
            $stagiaireIds = DB::table('formateur_stagiaire')
                ->where('formateur_id', $user->id)
                ->pluck('stagiaire_id');
        } elseif ($isCommercial) {
            $stagiaireIds = DB::table('commercial_stagiaire')
                ->where('commercial_id', $user->id)
                ->pluck('stagiaire_id');
        }

        $today = Carbon::today();

        // 1. Formation Reminders (J-7, J-4, J-1)
        $formationStats = [];
        foreach ([7, 4, 1] as $days) {
            $targetDate = $today->copy()->addDays($days)->toDateString();
            $query = Stagiaire::whereDate('date_debut_formation', $targetDate);
            if ($stagiaireIds) {
                $query->whereIn('id', $stagiaireIds);
            }
            $formationStats["j-{$days}"] = $query->count();
        }

        // 2. Quiz Inactivity (7d, 30d, 90d)
        $inactiveStats = [];
        foreach ([7, 30, 90] as $days) {
            $threshold = Carbon::now()->subDays($days);
            
            $query = User::role('stagiaire');
            if ($stagiaireIds) {
                $query->whereHas('stagiaire', function($q) use ($stagiaireIds) {
                    $q->whereIn('id', $stagiaireIds);
                });
            }

            // Users whose last participation is before threshold
            $count = $query->whereHas('quizParticipations', function($q) use ($threshold) {
                $q->whereNotNull('completed_at')
                  ->where('completed_at', '<=', $threshold);
            })->count();
            
            $inactiveStats["{$days}d"] = $count;
        }

        // 3. Registered but no quiz played (1d, 3d, 7d)
        $noQuizStats = [];
        foreach ([1, 3, 7] as $days) {
            $thresholdStart = Carbon::now()->subDays($days)->startOfDay();
            $thresholdEnd = Carbon::now()->subDays($days)->endOfDay();
            
            $query = User::role('stagiaire')
                ->whereBetween('created_at', [$thresholdStart, $thresholdEnd])
                ->whereDoesntHave('quizParticipations');
            
            if ($stagiaireIds) {
                $query->whereHas('stagiaire', function($q) use ($stagiaireIds) {
                    $q->whereIn('id', $stagiaireIds);
                });
            }
            
            $noQuizStats["{$days}d"] = $query->count();
        }

        // 4. Recent Sends (Last 24h)
        $recentSendsQuery = Notification::where('created_at', '>=', Carbon::now()->subHours(24))
            ->whereIn('type', ['formation', 'inactivity_quiz', 'first_quiz_reminder']);
        
        if ($stagiaireIds) {
            $userIds = Stagiaire::whereIn('id', $stagiaireIds)->pluck('user_id');
            $recentSendsQuery->whereIn('user_id', $userIds);
        }

        $recentSends = $recentSendsQuery->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        return response()->json([
            'formation' => $formationStats,
            'inactivity' => $inactiveStats,
            'no_quiz' => $noQuizStats,
            'recent_sends' => $recentSends,
            'last_run' => Carbon::now()->startOfDay()->setHour(8)->toDateTimeString(), // Command runs at 8am
        ]);
    }

    /**
     * Get history of auto-reminders
     */
    public function getHistory(Request $request)
    {
        $user = $request->user();
        $isFormateur = $user->role === 'formateur' || $user->role === 'formatrice';
        $isCommercial = $user->role === 'commercial' || $user->role === 'commerciale';

        $query = Notification::whereIn('type', ['formation', 'inactivity_quiz', 'first_quiz_reminder'])
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc');

        if ($isFormateur) {
            $stagiaireUserIds = DB::table('formateur_stagiaire')
                ->join('stagiaires', 'formateur_stagiaire.stagiaire_id', '=', 'stagiaires.id')
                ->where('formateur_id', $user->id)
                ->pluck('stagiaires.user_id');
            $query->whereIn('user_id', $stagiaireUserIds);
        } elseif ($isCommercial) {
            $stagiaireUserIds = DB::table('commercial_stagiaire')
                ->join('stagiaires', 'commercial_stagiaire.stagiaire_id', '=', 'stagiaires.id')
                ->where('commercial_id', $user->id)
                ->pluck('stagiaires.user_id');
            $query->whereIn('user_id', $stagiaireUserIds);
        }

        return response()->json($query->paginate(20));
    }
}
