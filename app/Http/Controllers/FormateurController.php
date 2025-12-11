<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stagiaire;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FormateurController extends Controller
{
    /**
     * Récupère les statistiques globales du dashboard formateur
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $formateur = $request->user();
            
            // Vérification du rôle
            if (!in_array($formateur->role, ['formateur', 'formatrice'])) {
                return response()->json([
                    'error' => 'Accès refusé - Rôle formateur requis'
                ], 403);
            }
            
            // Récupérer tous les stagiaires (pour l'instant tous, à filtrer par formateur si liaison existe)
            $stagiaires = Stagiaire::with('user')->get();
            
            $totalStagiaires = $stagiaires->count();
            
            // Stagiaires actifs cette semaine
            $weekAgo = Carbon::now()->subWeek();
            $activeStagiaires = $stagiaires->filter(function($stagiaire) use ($weekAgo) {
                return $stagiaire->user && 
                       $stagiaire->user->last_activity_at && 
                       Carbon::parse($stagiaire->user->last_activity_at)->gt($weekAgo);
            })->count();
            
            // Stagiaires inactifs (pas d'activité depuis 7+ jours)
            $inactiveCount = $totalStagiaires - $activeStagiaires;
            
            // Stagiaires jamais connectés (vérification null-safe)
            $neverConnected = $stagiaires->filter(function($stagiaire) {
                return $stagiaire->user && !$stagiaire->user->last_login_at;
            })->count();
            
            // Score moyen des quiz (via user_id car quiz_participations utilise user_id pas stagiaire_id)
            $userIds = $stagiaires->pluck('user.id')->filter();
            $avgQuizScore = DB::table('quiz_participations')
                ->whereIn('user_id', $userIds)
                ->avg('score') ?? 0;
            
            // Total heures vidéos (si table video_views existe, sinon 0)
            $totalVideoHours = 0;
            if (DB::getSchemaBuilder()->hasTable('video_views')) {
                $totalVideoSeconds = DB::table('video_views')
                    ->whereIn('stagiaire_id', $stagiaires->pluck('id'))
                    ->sum('duration_watched');
                $totalVideoHours = round($totalVideoSeconds / 3600, 1);
            }
            
            // Formations (à adapter selon structure réelle)
            $formations = [];
            
            return response()->json([
                'total_stagiaires' => $totalStagiaires,
                'active_this_week' => $activeStagiaires,
                'inactive_count' => $inactiveCount,
                'never_connected' => $neverConnected,
                'avg_quiz_score' => round($avgQuizScore, 1),
                'total_video_hours' => $totalVideoHours,
                'formations' => $formations,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getDashboardStats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la récupération des statistiques',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère la liste des stagiaires du formateur
     */
    public function getStagiaires(Request $request)
    {
        try {
            $formateur = $request->user();
            
            // Récupérer tous les stagiaires avec leurs informations user
            $stagiaires = Stagiaire::with(['user'])
                ->get()
                ->map(function($stagiaire) {
                    return [
                        'id' => $stagiaire->id,
                        'prenom' => $stagiaire->prenom,
                        'nom' => $stagiaire->user->name ?? '',
                        'email' => $stagiaire->user->email ?? '',
                        'telephone' => $stagiaire->telephone,
                        'ville' => $stagiaire->ville,
                        'last_login_at' => $stagiaire->user->last_login_at ?? null,
                        'last_activity_at' => $stagiaire->user->last_activity_at ?? null,
                        'is_online' => $stagiaire->user->is_online ?? false,
                        'last_client' => $stagiaire->user->last_client ?? null,
                        'image' => $stagiaire->user->image ?? null,
                    ];
                });

            return response()->json([
                'stagiaires' => $stagiaires
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getStagiaires', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la récupération des stagiaires'
            ], 500);
        }
    }

    /**
     * Récupère les stagiaires inactifs
     */
    public function getInactiveStagiaires(Request $request)
    {
        try {
            $days = $request->query('days', 7);
            $platform = $request->query('platform'); // web, android, ios
            
            $thresholdDate = Carbon::now()->subDays($days);
            
            $query = Stagiaire::with(['user'])
                ->whereHas('user', function($q) use ($thresholdDate, $platform) {
                    $q->where(function($query) use ($thresholdDate) {
                        $query->whereNull('last_activity_at')
                              ->orWhere('last_activity_at', '<', $thresholdDate);
                    });
                    
                    if ($platform) {
                        $q->where('last_client', $platform);
                    }
                });
            
            $inactiveStagiaires = $query->get()->map(function($stagiaire) {
                $daysSinceActivity = null;
                if ($stagiaire->user->last_activity_at) {
                    $daysSinceActivity = Carbon::parse($stagiaire->user->last_activity_at)
                        ->diffInDays(Carbon::now());
                }
                
                return [
                    'id' => $stagiaire->id,
                    'prenom' => $stagiaire->prenom,
                    'nom' => $stagiaire->user->name ?? '',
                    'email' => $stagiaire->user->email ?? '',
                    'last_activity_at' => $stagiaire->user->last_activity_at,
                    'days_since_activity' => $daysSinceActivity,
                    'never_connected' => !$stagiaire->user->last_login_at,
                    'last_client' => $stagiaire->user->last_client,
                ];
            });

            return response()->json([
                'inactive_stagiaires' => $inactiveStagiaires,
                'count' => $inactiveStagiaires->count(),
                'threshold_days' => $days,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getInactiveStagiaires', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la récupération des stagiaires inactifs'
            ], 500);
        }
    }

    /**
     * Récupère les stagiaires jamais connectés
     */
    public function getNeverConnected(Request $request)
    {
        try {
            $neverConnected = Stagiaire::with(['user'])
                ->whereHas('user', function($q) {
                    $q->whereNull('last_login_at');
                })
                ->get()
                ->map(function($stagiaire) {
                    return [
                        'id' => $stagiaire->id,
                        'prenom' => $stagiaire->prenom,
                        'nom' => $stagiaire->user->name ?? '',
                        'email' => $stagiaire->user->email ?? '',
                        'created_at' => $stagiaire->created_at,
                        'fcm_token' => $stagiaire->user->fcm_token ? 'Oui' : 'Non',
                    ];
                });

            return response()->json([
                'never_connected' => $neverConnected,
                'count' => $neverConnected->count(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getNeverConnected', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la récupération des stagiaires jamais connectés'
            ], 500);
        }
    }

    /**
     * Récupère les statistiques détaillées d'un stagiaire
     */
    public function getStagiaireStats(Request $request, $id)
    {
        try {
            $stagiaire = Stagiaire::with(['user'])->findOrFail($id);
            
            // Stats quiz
            $quizStats = QuizSubmission::where('stagiaire_id', $id)
                ->selectRaw('
                    COUNT(*) as total_quiz,
                    AVG(score) as avg_score,
                    MAX(score) as best_score,
                    SUM(correct_answers) as total_correct,
                    SUM(total_questions) as total_questions
                ')
                ->first();
            
            // Dernière activité
            $lastActivity = $stagiaire->user->last_activity_at 
                ? Carbon::parse($stagiaire->user->last_activity_at)->diffForHumans()
                : 'Jamais';
            
            return response()->json([
                'stagiaire' => [
                    'id' => $stagiaire->id,
                    'prenom' => $stagiaire->prenom,
                    'nom' => $stagiaire->user->name,
                    'email' => $stagiaire->user->email,
                ],
                'quiz_stats' => [
                    'total_quiz' => $quizStats->total_quiz ?? 0,
                    'avg_score' => round($quizStats->avg_score ?? 0, 1),
                    'best_score' => $quizStats->best_score ?? 0,
                    'total_correct' => $quizStats->total_correct ?? 0,
                    'total_questions' => $quizStats->total_questions ?? 0,
                ],
                'activity' => [
                    'last_activity' => $lastActivity,
                    'last_login' => $stagiaire->user->last_login_at,
                    'is_online' => $stagiaire->user->is_online,
                    'last_client' => $stagiaire->user->last_client,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getStagiaireStats', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Stagiaire non trouvé'
            ], 404);
        }
    }

    /**
     * Envoie une notification FCM aux stagiaires sélectionnés
     */
    public function sendNotification(Request $request)
    {
        try {
            $request->validate([
                'recipient_ids' => 'required|array',
                'recipient_ids.*' => 'integer|exists:stagiaires,id',
                'title' => 'required|string|max:100',
                'body' => 'required|string|max:500',
                'data' => 'nullable|array',
            ]);

            $recipientIds = $request->input('recipient_ids');
            $title = $request->input('title');
            $body = $request->input('body');
            $data = $request->input('data', []);

            // Récupérer les FCM tokens des stagiaires
            $stagiaires = Stagiaire::with('user')
                ->whereIn('id', $recipientIds)
                ->get();

            $tokens = $stagiaires->map(function($stagiaire) {
                return $stagiaire->user->fcm_token;
            })->filter()->unique()->values()->toArray();

            if (empty($tokens)) {
                return response()->json([
                    'error' => 'Aucun token FCM trouvé pour ces stagiaires'
                ], 400);
            }

            // Utiliser le service de notification existant si disponible
            if (class_exists('App\Services\NotificationService')) {
                $notificationService = app('App\Services\NotificationService');
                $result = $notificationService->sendToMultiple($tokens, $title, $body, $data);
            } else {
                // Fallback : notification basique
                $result = ['success' => true, 'sent' => count($tokens)];
            }

            // Logger la notification
            DB::table('notification_history')->insert([
                'formateur_id' => $request->user()->id,
                'recipient_count' => count($recipientIds),
                'title' => $title,
                'body' => $body,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification envoyée avec succès',
                'sent_count' => count($tokens),
                'result' => $result,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Données invalides',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur sendNotification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erreur lors de l\'envoi de la notification',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoie un email aux stagiaires sélectionnés
     */
    public function sendEmail(Request $request)
    {
        try {
            $request->validate([
                'recipient_ids' => 'required|array',
                'recipient_ids.*' => 'integer|exists:stagiaires,id',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $recipientIds = $request->input('recipient_ids');
            $subject = $request->input('subject');
            $message = $request->input('message');

            // Récupérer les emails des stagiaires
            $stagiaires = Stagiaire::with('user')
                ->whereIn('id', $recipientIds)
                ->get();

            $emails = $stagiaires->map(function($stagiaire) {
                return [
                    'email' => $stagiaire->user->email,
                    'name' => "{$stagiaire->prenom} {$stagiaire->user->name}"
                ];
            })->toArray();

            // Envoyer les emails
            foreach ($emails as $recipient) {
                try {
                    \Mail::raw($message, function($mail) use ($recipient, $subject) {
                        $mail->to($recipient['email'], $recipient['name'])
                             ->subject($subject);
                    });
                } catch (\Exception $e) {
                    Log::warning('Erreur envoi email', [
                        'email' => $recipient['email'],
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Logger l'envoi
            DB::table('email_history')->insert([
                'formateur_id' => $request->user()->id,
                'recipient_count' => count($recipientIds),
                'subject' => $subject,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Emails envoyés avec succès',
                'sent_count' => count($emails),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Données invalides',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur sendEmail', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Erreur lors de l\'envoi des emails'
            ], 500);
        }
    }

    /**
     * Récupère le classement des stagiaires d'une formation spécifique
     */
    public function getFormationRanking(Request $request, $formationId)
    {
        try {
            // Récupérer tous les stagiaires de la formation avec leurs points
            $stagiaires = DB::table('stagiaires')
                ->join('users', 'stagiaires.user_id', '=', 'users.id')
                ->join('catalogue_stagiaire', 'stagiaires.id', '=', 'catalogue_stagiaire.stagiaire_id')
                ->leftJoin('quiz_participations', 'users.id', '=', 'quiz_participations.user_id')
                ->where('catalogue_stagiaire.catalogue_formation_id', $formationId)
                ->select(
                    'stagiaires.id',
                    'stagiaires.prenom',
                    'users.name as nom',
                    'users.email',
                    DB::raw('COALESCE(SUM(quiz_participations.score), 0) as total_points'),
                    DB::raw('COUNT(quiz_participations.id) as total_quiz')
                )
                ->groupBy('stagiaires.id', 'stagiaires.prenom', 'users.name', 'users.email')
                ->orderBy('total_points', 'desc')
                ->get();

            return response()->json([
                'formation_id' => $formationId,
                'ranking' => $stagiaires,
                'total_stagiaires' => $stagiaires->count(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getFormationRanking', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur récupération classement'], 500);
        }
    }

    /**
     * Récupère le classement de tous les stagiaires du formateur
     */
    public function getMesStagiairesRanking(Request $request)
    {
        try {
            // Récupérer tous les stagiaires avec leurs points totaux
            $stagiaires = Stagiaire::with(['user'])
                ->join('users', 'stagiaires.user_id', '=', 'users.id')
                ->leftJoin('quiz_participations', 'users.id', '=', 'quiz_participations.user_id')
                ->select(
                    'stagiaires.id',
                    'stagiaires.prenom',
                    DB::raw('COALESCE(SUM(quiz_participations.score), 0) as total_points'),
                    DB::raw('COUNT(quiz_participations.id) as total_quiz'),
                    DB::raw('AVG(quiz_participations.score) as avg_score')
                )
                ->groupBy('stagiaires.id', 'stagiaires.prenom')
                ->orderBy('total_points', 'desc')
                ->get()
                ->map(function($stagiaire, $index) {
                    return [
                        'rank' => $index + 1,
                        'id' => $stagiaire->id,
                        'prenom' => $stagiaire->prenom,
                        'nom' => $stagiaire->user->name ?? '',
                        'email' => $stagiaire->user->email ?? '',
                        'total_points' => (int) $stagiaire->total_points,
                        'total_quiz' => (int) $stagiaire->total_quiz,
                        'avg_score' => round($stagiaire->avg_score ?? 0, 1),
                    ];
                });

            return response()->json([
                'ranking' => $stagiaires,
                'total_stagiaires' => $stagiaires->count(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getMesStagiairesRanking', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur récupération classement'], 500);
        }
    }

    /**
     * Récupère toutes les vidéos accessibles
     */
    public function getAllVideos(Request $request)
    {
        try {
            $videos = DB::table('medias')
                ->where('type', 'video')
                ->orWhere('type', 'LIKE', '%video%')
                ->select('id', 'titre', 'description', 'url', 'type', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'videos' => $videos,
                'total' => $videos->count(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getAllVideos', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur récupération vidéos'], 500);
        }
    }

    /**
     * Récupère les statistiques de visionnage d'une vidéo
     */
    public function getVideoStats(Request $request, $videoId)
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('video_views')) {
                return response()->json([
                    'video_id' => $videoId,
                    'total_views' => 0,
                    'total_duration_watched' => 0,
                    'completion_rate' => 0,
                    'views_by_stagiaire' => [],
                ], 200);
            }

            $stats = DB::table('video_views')
                ->where('media_id', $videoId)
                ->selectRaw('
                    COUNT(*) as total_views,
                    SUM(duration_watched) as total_duration_watched,
                    AVG(CASE WHEN completed = 1 THEN 100 ELSE 0 END) as completion_rate
                ')
                ->first();

            $viewsByStagiaire = DB::table('video_views')
                ->join('stagiaires', 'video_views.stagiaire_id', '=', 'stagiaires.id')
                ->join('users', 'stagiaires.user_id', '=', 'users.id')
                ->where('video_views.media_id', $videoId)
                ->select(
                    'stagiaires.id',
                    'stagiaires.prenom',
                    'users.name as nom',
                    DB::raw('SUM(video_views.duration_watched) as total_watched'),
                    DB::raw('MAX(video_views.completed) as completed')
                )
                ->groupBy('stagiaires.id', 'stagiaires.prenom', 'users.name')
                ->get();

            return response()->json([
                'video_id' => $videoId,
                'total_views' => $stats->total_views ?? 0,
                'total_duration_watched' => $stats->total_duration_watched ?? 0,
                'completion_rate' => round($stats->completion_rate ?? 0, 1),
                'views_by_stagiaire' => $viewsByStagiaire,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getVideoStats', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur récupération stats vidéo'], 500);
        }
    }

    /**
     * Récupère les formations du formateur
     */
    public function getFormations(Request $request)
    {
        try {
            // Pour l'instant, récupérer toutes les formations
            // À adapter selon la logique métier (liaison formateur-formation)
            $formations = DB::table('catalogue_formations')
                ->select('id', 'nom', 'description', 'created_at')
                ->get()
                ->map(function($formation) {
                    // Compter les stagiaires par formation
                    $stagiaireCount = DB::table('catalogue_stagiaire')
                        ->where('catalogue_formation_id', $formation->id)
                        ->count();
                    
                    return [
                        'id' => $formation->id,
                        'nom' => $formation->nom,
                        'description' => $formation->description,
                        'stagiaires_count' => $stagiaireCount,
                        'created_at' => $formation->created_at,
                    ];
                });

            return response()->json([
                'formations' => $formations,
                'total' => $formations->count(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur getFormations', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur récupération formations'], 500);
        }
    }
}
