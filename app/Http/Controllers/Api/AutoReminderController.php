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
use Illuminate\Support\Facades\Cache;

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
                ->pluck('stagiaire_id')->toArray();
        } elseif ($isCommercial) {
            $stagiaireIds = DB::table('commercial_stagiaire')
                ->where('commercial_id', $user->id)
                ->pluck('stagiaire_id')->toArray();
        }

        $cacheKey = 'auto_reminders_stats_' . ($stagiaireIds ? md5(json_encode($stagiaireIds)) : 'admin');
        
        return Cache::remember($cacheKey, 300, function() use ($stagiaireIds) {
            $today = Carbon::today();
            $now = Carbon::now();

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

            // 1b. Formation End Reminders (J-3)
            $targetDateEnd = $today->copy()->addDays(3)->toDateString();
            $queryEnd = Stagiaire::whereDate('date_fin_formation', $targetDateEnd);
            if ($stagiaireIds) {
                $queryEnd->whereIn('id', $stagiaireIds);
            }
            $formationStats["end-j-3"] = $queryEnd->count();

            // 2. Quiz Inactivity (7d, 30d, 90d) - Corrected logic: Last participation <= threshold
            $inactiveStats = [];
            
            // Subquery to get last participation date for each stagiaire
            $lastParticipations = DB::table('quiz_participations')
                ->select('user_id', DB::raw('MAX(completed_at) as last_at'))
                ->whereNotNull('completed_at')
                ->groupBy('user_id');

            foreach ([7, 30, 90] as $days) {
                $threshold = Carbon::now()->subDays($days);
                
                $query = User::role('stagiaire')
                    ->joinSub($lastParticipations, 'last_p', function ($join) {
                        $join->on('users.id', '=', 'last_p.user_id');
                    })
                    ->where('last_p.last_at', '<=', $threshold);

                if ($stagiaireIds) {
                    $query->whereHas('stagiaire', function($q) use ($stagiaireIds) {
                        $q->whereIn('id', $stagiaireIds);
                    });
                }

                $inactiveStats["{$days}d"] = $query->count();
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
                ->whereIn('type', ['formation', 'formation_end', 'inactivity_quiz', 'first_quiz_reminder']);
            
            if ($stagiaireIds) {
                $userIds = Stagiaire::whereIn('id', $stagiaireIds)->pluck('user_id');
                $recentSendsQuery->whereIn('user_id', $userIds);
            }

            $recentSends = $recentSendsQuery->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type');

            return [
                'formation' => $formationStats,
                'inactivity' => $inactiveStats,
                'no_quiz' => $noQuizStats,
                'recent_sends' => $recentSends,
                'last_run' => Cache::get('auto_reminders_last_run', Carbon::now()->startOfDay()->setHour(8)->toDateTimeString()),
            ];
        });
    }

    /**
     * Get history of auto-reminders
     */
    public function getHistory(Request $request)
    {
        $user = $request->user();
        $isFormateur = $user->role === 'formateur' || $user->role === 'formatrice';
        $isCommercial = $user->role === 'commercial' || $user->role === 'commerciale';

        $query = Notification::whereIn('type', ['formation', 'formation_end', 'inactivity_quiz', 'first_quiz_reminder'])
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

    /**
     * Get list of users targeted for next reminders
     */
    public function getTargetedUsers(Request $request)
    {
        $user = $request->user();
        $isFormateur = $user->role === 'formateur' || $user->role === 'formatrice';
        $isCommercial = $user->role === 'commercial' || $user->role === 'commerciale';
        
        $stagiaireIds = null;
        if ($isFormateur) {
            $stagiaireIds = DB::table('formateur_stagiaire')->where('formateur_id', $user->id)->pluck('stagiaire_id');
        } elseif ($isCommercial) {
            $stagiaireIds = DB::table('commercial_stagiaire')->where('commercial_id', $user->id)->pluck('stagiaire_id');
        }

        $today = Carbon::today();
        $targeted = [];

        // 1. Formation
        foreach ([7, 4, 1] as $days) {
            $targetDate = $today->copy()->addDays($days)->toDateString();
            $query = Stagiaire::whereDate('date_debut_formation', $targetDate)->with('user:id,name,email');
            if ($stagiaireIds) $query->whereIn('id', $stagiaireIds);
            
            foreach ($query->get() as $s) {
                if ($s->user) {
                    $targeted[] = [
                        'user' => $s->user,
                        'reason' => "Formation dans {$days} jour(s)",
                        'type' => 'formation'
                    ];
                }
            }
        }

        // 1b. Formation End (J-3)
        $targetDateEnd = $today->copy()->addDays(3)->toDateString();
        $queryEnd = Stagiaire::whereDate('date_fin_formation', $targetDateEnd)->with('user:id,name,email');
        if ($stagiaireIds) $queryEnd->whereIn('id', $stagiaireIds);
        foreach ($queryEnd->get() as $s) {
            if ($s->user) {
                $targeted[] = [
                    'user' => $s->user,
                    'reason' => "Fin de formation dans 3 jours",
                    'type' => 'formation_end'
                ];
            }
        }

        // 2. Inactivity (simplified/sample for preview)
        // Note: Real logic is more complex with deduplication, but here we show "potential" targets
        foreach ([7, 30, 90] as $days) {
            $threshold = Carbon::now()->subDays($days);
            $query = User::role('stagiaire')->with('stagiaire')
                ->whereHas('quizParticipations', function($q) use ($threshold) {
                    $q->whereNotNull('completed_at')->where('completed_at', '<=', $threshold);
                });
            
            if ($stagiaireIds) {
                $query->whereHas('stagiaire', function($q) use ($stagiaireIds) {
                    $q->whereIn('id', $stagiaireIds);
                });
            }

            foreach ($query->limit(20)->get() as $u) {
                $targeted[] = [
                    'user' => $u,
                    'reason' => "Inactif depuis {$days} jours",
                    'type' => 'inactivity_quiz'
                ];
            }
        }

        return response()->json($targeted);
    }

    /**
     * Manually trigger the auto-reminders command
     */
    public function runManualReminders(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'administrateur' && $user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        try {
            \Illuminate\Support\Facades\Artisan::call('notify:scheduled');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            return response()->json([
                'message' => 'Command exécutée avec succès',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'exécution de la commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
