<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\CatalogueFormation;
use App\Models\QuizParticipation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormateurAlertsController extends Controller
{
    /**
     * Vérifier que l'utilisateur est un formateur
     */
    private function checkFormateur()
    {
        $user = Auth::user();
        if ($user->role !== 'formateur' && $user->role !== 'formatrice') {
            abort(403, 'Accès réservé aux formateurs.');
        }

        if (!$user->formateur) {
            abort(404, 'Profil formateur non trouvé.');
        }
    }

    /**
     * API: Get all intelligent alerts for the formateur dashboard
     * GET /formateur/alerts
     */
    public function getAlerts()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $alerts = [];

        // 1. Inactive Students Alert (no activity in last 7 days)
        $inactiveStagiaires = $formateur->stagiaires()
            ->whereDoesntHave('quizParticipations', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(7));
            })
            ->where('statut', 1)
            ->with('user')
            ->get();

        foreach ($inactiveStagiaires as $stagiaire) {
            $alerts[] = [
                'id' => 'inactive_' . $stagiaire->id,
                'type' => 'warning',
                'category' => 'inactivity',
                'title' => 'Stagiaire inactif',
                'message' => "{$stagiaire->user->prenom} {$stagiaire->user->nom} n'a pas participé depuis 7 jours",
                'stagiaire_id' => $stagiaire->id,
                'stagiaire_name' => "{$stagiaire->user->prenom} {$stagiaire->user->nom}",
                'priority' => 'medium',
                'created_at' => Carbon::now()->toIso8601String(),
            ];
        }

        // 2. Approaching Deadlines (formations ending in next 7 days)
        $approachingDeadlines = $formateur->stagiaires()
            ->whereNotNull('date_fin_formation')
            ->where('date_fin_formation', '<=', Carbon::now()->addDays(7))
            ->where('date_fin_formation', '>=', Carbon::now())
            ->with('user')
            ->get();

        foreach ($approachingDeadlines as $stagiaire) {
            $daysLeft = Carbon::now()->diffInDays(Carbon::parse($stagiaire->date_fin_formation));
            $alerts[] = [
                'id' => 'deadline_' . $stagiaire->id,
                'type' => 'info',
                'category' => 'deadline',
                'title' => 'Deadline approchante',
                'message' => "Formation de {$stagiaire->user->prenom} {$stagiaire->user->nom} se termine dans {$daysLeft} jour(s)",
                'stagiaire_id' => $stagiaire->id,
                'stagiaire_name' => "{$stagiaire->user->prenom} {$stagiaire->user->nom}",
                'days_left' => $daysLeft,
                'priority' => $daysLeft <= 3 ? 'high' : 'medium',
                'created_at' => Carbon::now()->toIso8601String(),
            ];
        }

        // 3. Low Performance Students (average quiz score < 50% in last 30 days)
        $stagiaireIds = $formateur->stagiaires()->pluck('stagiaires.id');
        
        $lowPerformers = QuizParticipation::whereIn('stagiaire_id', $stagiaireIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select('stagiaire_id', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as attempts'))
            ->groupBy('stagiaire_id')
            ->having('avg_score', '<', 50)
            ->having('attempts', '>=', 3) // at least 3 attempts
            ->get();

        foreach ($lowPerformers as $performer) {
            $stagiaire = Stagiaire::with('user')->find($performer->stagiaire_id);
            if ($stagiaire) {
                $alerts[] = [
                    'id' => 'low_performance_' . $stagiaire->id,
                    'type' => 'danger',
                    'category' => 'performance',
                    'title' => 'Performance faible',
                    'message' => "{$stagiaire->user->prenom} {$stagiaire->user->nom} a un score moyen de " . round($performer->avg_score, 1) . "% sur {$performer->attempts} quiz",
                    'stagiaire_id' => $stagiaire->id,
                    'stagiaire_name' => "{$stagiaire->user->prenom} {$stagiaire->user->nom}",
                    'avg_score' => round($performer->avg_score, 1),
                    'priority' => 'high',
                    'created_at' => Carbon::now()->toIso8601String(),
                ];
            }
        }

        // 4. High Dropout Rate Alert (>60% abandonment for specific students)
        $highDropout = QuizParticipation::whereIn('stagiaire_id', $stagiaireIds)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                'stagiaire_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status != "completed" THEN 1 ELSE 0 END) as abandoned')
            )
            ->groupBy('stagiaire_id')
            ->having('total', '>=', 3)
            ->get()
            ->filter(function ($item) {
                return ($item->abandoned / $item->total) > 0.6;
            });

        foreach ($highDropout as $dropout) {
            $stagiaire = Stagiaire::with('user')->find($dropout->stagiaire_id);
            if ($stagiaire) {
                $dropoutRate = round(($dropout->abandoned / $dropout->total) * 100, 1);
                $alerts[] = [
                    'id' => 'dropout_' . $stagiaire->id,
                    'type' => 'warning',
                    'category' => 'dropout',
                    'title' => 'Taux d\'abandon élevé',
                    'message' => "{$stagiaire->user->prenom} {$stagiaire->user->nom} abandonne {$dropoutRate}% des quiz",
                    'stagiaire_id' => $stagiaire->id,
                    'stagiaire_name' => "{$stagiaire->user->prenom} {$stagiaire->user->nom}",
                    'dropout_rate' => $dropoutRate,
                    'priority' => 'high',
                    'created_at' => Carbon::now()->toIso8601String(),
                ];
            }
        }

        // 5. Never Connected Students
        $neverConnected = $formateur->stagiaires()
            ->whereDoesntHave('quizParticipations')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->with('user')
            ->get();

        foreach ($neverConnected as $stagiaire) {
            $daysAgo = Carbon::now()->diffInDays(Carbon::parse($stagiaire->created_at));
            $alerts[] = [
                'id' => 'never_connected_' . $stagiaire->id,
                'type' => 'danger',
                'category' => 'never_connected',
                'title' => 'Jamais connecté',
                'message' => "{$stagiaire->user->prenom} {$stagiaire->user->nom} n'a jamais participé (inscrit il y a {$daysAgo} jours)",
                'stagiaire_id' => $stagiaire->id,
                'stagiaire_name' => "{$stagiaire->user->prenom} {$stagiaire->user->nom}",
                'days_since_registration' => $daysAgo,
                'priority' => 'high',
                'created_at' => Carbon::now()->toIso8601String(),
            ];
        }

        // Sort by priority
        $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
        usort($alerts, function ($a, $b) use ($priorityOrder) {
            return $priorityOrder[$a['priority']] <=> $priorityOrder[$b['priority']];
        });

        return response()->json([
            'alerts' => $alerts,
            'total_count' => count($alerts),
            'high_priority_count' => count(array_filter($alerts, fn($a) => $a['priority'] === 'high')),
        ]);
    }

    /**
     * API: Get alert statistics
     * GET /formateur/alerts/stats
     */
    public function getAlertStats()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $stagiaireIds = $formateur->stagiaires()->pluck('stagiaires.id');

        // Count inactive
        $inactiveCount = $formateur->stagiaires()
            ->whereDoesntHave('quizParticipations', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(7));
            })
            ->where('statut', 1)
            ->count();

        // Count approaching deadlines
        $deadlineCount = $formateur->stagiaires()
            ->whereNotNull('date_fin_formation')
            ->where('date_fin_formation', '<=', Carbon::now()->addDays(7))
            ->where('date_fin_formation', '>=', Carbon::now())
            ->count();

        // Count low performers
        $lowPerformersCount = QuizParticipation::whereIn('stagiaire_id', $stagiaireIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select('stagiaire_id', DB::raw('AVG(score) as avg_score'))
            ->groupBy('stagiaire_id')
            ->having('avg_score', '<', 50)
            ->count();

        // Count never connected
        $neverConnectedCount = $formateur->stagiaires()
            ->whereDoesntHave('quizParticipations')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->count();

        return response()->json([
            'stats' => [
                'inactive' => $inactiveCount,
                'approaching_deadline' => $deadlineCount,
                'low_performance' => $lowPerformersCount,
                'never_connected' => $neverConnectedCount,
                'total' => $inactiveCount + $deadlineCount + $lowPerformersCount + $neverConnectedCount,
            ],
        ]);
    }
}
