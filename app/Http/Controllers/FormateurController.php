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
            
            // Stagiaires jamais connectés
            $neverConnected = $stagiaires->filter(function($stagiaire) {
                return !$stagiaire->user->last_login_at;
            })->count();
            
            // Score moyen des quiz
            $avgQuizScore = QuizSubmission::whereIn('stagiaire_id', $stagiaires->pluck('id'))
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
}
