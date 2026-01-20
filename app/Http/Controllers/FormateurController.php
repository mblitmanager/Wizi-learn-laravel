<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stagiaire;
use App\Models\Formateur;
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
            
            // Récupérer l'ID du formateur
            $formateurModel = Formateur::where('user_id', $formateur->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;
            
            // Filtrer les stagiaires assignés au formateur via formateur_stagiaire
            $stagiairesQuery = Stagiaire::with('user');
            if ($formateurId) {
                $stagiairesQuery->whereHas('formateurs', function($query) use ($formateurId) {
                    $query->where('formateurs.id', $formateurId);
                });
            }
            $stagiaires = $stagiairesQuery->get();
            
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
            
            // Stats par formation (avec pagination)
            $formationsQuery = DB::table('catalogue_formations')
                ->leftJoin('stagiaire_catalogue_formations', 'catalogue_formations.id', '=', 'stagiaire_catalogue_formations.catalogue_formation_id')
                ->leftJoin('stagiaires', 'stagiaire_catalogue_formations.stagiaire_id', '=', 'stagiaires.id')
                ->leftJoin('users', 'stagiaires.user_id', '=', 'users.id')
                ->leftJoin('quiz_participations', 'users.id', '=', 'quiz_participations.user_id')
                ->select(
                    'catalogue_formations.id',
                    'catalogue_formations.titre as nom',
                    DB::raw('COUNT(DISTINCT stagiaires.id) as total_stagiaires'),
                    DB::raw('COUNT(DISTINCT CASE WHEN users.last_activity_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN stagiaires.id END) as stagiaires_actifs'),
                    DB::raw('COALESCE(AVG(quiz_participations.score), 0) as score_moyen')
                )
                ->groupBy('catalogue_formations.id', 'catalogue_formations.titre')
                ->orderBy('total_stagiaires', 'desc');
            
            $formationsPerPage = $request->input('formations_per_page', 10);
            $formationsPaginated = $formationsQuery->paginate($formationsPerPage, ['*'], 'formations_page');
            
            $formations = [
                'data' => $formationsPaginated->items(),
                'current_page' => $formationsPaginated->currentPage(),
                'last_page' => $formationsPaginated->lastPage(),
                'per_page' => $formationsPaginated->perPage(),
                'total' => $formationsPaginated->total(),
            ];

            // Stats par formateur (avec pagination si table existe)
            $statsFormateurs = ['data' => [], 'current_page' => 1, 'last_page' => 1, 'per_page' => 10, 'total' => 0];
            if (DB::getSchemaBuilder()->hasTable('formateurs')) {
                $formateursQuery = DB::table('formateurs')
                    ->join('users', 'formateurs.user_id', '=', 'users.id')
                    ->leftJoin('formateur_stagiaire', 'formateurs.id', '=', 'formateur_stagiaire.formateur_id')
                    ->leftJoin('stagiaires', 'formateur_stagiaire.stagiaire_id', '=', 'stagiaires.id')
                    ->select(
                        'formateurs.id',
                        'formateurs.prenom',
                        'users.name as nom',
                        DB::raw('COUNT(DISTINCT stagiaires.id) as total_stagiaires')
                    )
                    ->groupBy('formateurs.id', 'formateurs.prenom', 'users.name')
                    ->orderBy('total_stagiaires', 'desc');
                
                $formateursPerPage = $request->input('formateurs_per_page', 10);
                $formateursPaginated = $formateursQuery->paginate($formateursPerPage, ['*'], 'formateurs_page');
                
                $statsFormateurs = [
                    'data' => $formateursPaginated->items(),
                    'current_page' => $formateursPaginated->currentPage(),
                    'last_page' => $formateursPaginated->lastPage(),
                    'per_page' => $formateursPaginated->perPage(),
                    'total' => $formateursPaginated->total(),
                ];
            }
            
            // Total formations assignées
            $totalFormations = DB::table('catalogue_formations')
                ->join('formateur_catalogue_formation', 'catalogue_formations.id', '=', 'formateur_catalogue_formation.catalogue_formation_id')
                ->where('formateur_catalogue_formation.formateur_id', $formateurId)
                ->count();
            
            if ($totalFormations == 0) {
                // Fallback: check if there's a simpler assignment or if they are assigned directly to stagiaires
                $totalFormations = DB::table('catalogue_formations')
                    ->join('stagiaire_catalogue_formations', 'catalogue_formations.id', '=', 'stagiaire_catalogue_formations.catalogue_formation_id')
                    ->join('formateur_stagiaire', 'stagiaire_catalogue_formations.stagiaire_id', '=', 'formateur_stagiaire.stagiaire_id')
                    ->where('formateur_stagiaire.formateur_id', $formateurId)
                    ->distinct('catalogue_formations.id')
                    ->count('catalogue_formations.id');
            }

            // Nombre total de quiz terminés par ses stagiaires
            $totalQuizzesTaken = DB::table('quiz_participations')
                ->whereIn('user_id', $userIds)
                ->where('status', 'completed')
                ->count();
            
            return response()->json([
                'total_stagiaires' => $totalStagiaires,
                'total_formations' => $totalFormations,
                'total_quizzes_taken' => $totalQuizzesTaken,
                'active_this_week' => $activeStagiaires,
                'inactive_count' => $inactiveCount,
                'never_connected' => $neverConnected,
                'avg_quiz_score' => round($avgQuizScore, 1),
                'total_video_hours' => $totalVideoHours,
                'formations' => $formations,
                'formateurs' => $statsFormateurs,
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
     * Récupère la liste des stagiaires actuellement en ligne
     */
    public function getOnlineStagiaires(Request $request)
    {
        try {
            $user = $request->user();
            $formateurModel = Formateur::where('user_id', $user->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;

            if (!$formateurId) {
                return response()->json(['stagiaires' => [], 'total' => 0], 200);
            }

            // Get stagiaires linked directly only (strict parity with Node.js)
            $onlineStagiaires = Stagiaire::with(['user', 'catalogue_formations'])
                ->where(function($q) use ($formateurId) {
                    // Directly linked only
                    $q->whereHas('formateurs', function($query) use ($formateurId) {
                        $query->where('formateurs.id', $formateurId);
                    });
                    // OR via formations - COMMENTED for strict parity
                    // ->orWhereHas('catalogue_formations', function($query) use ($formateurId) {
                    //     $query->whereHas('formateurs', function($sub) use ($formateurId) {
                    //         $sub->where('formateurs.id', $formateurId);
                    //     });
                    // });
                })
                ->whereHas('user', function($query) {
                    $query->where('is_online', 1);
                })
                ->get()
                ->map(function($stagiaire) {
                    $formations = $stagiaire->catalogue_formations->pluck('titre')->toArray();
                    return [
                        'id' => $stagiaire->id,
                        'prenom' => $stagiaire->prenom,
                        'nom' => $stagiaire->user->name ?? '',
                        'email' => $stagiaire->user->email ?? '',
                        'avatar' => $stagiaire->user->image ?? $stagiaire->user->avatar ?? null,
                        'last_activity_at' => $stagiaire->user->last_activity_at,
                        'formations' => $formations,
                    ];
                });

            return response()->json([
                'stagiaires' => $onlineStagiaires,
                'total' => $onlineStagiaires->count(),
            ], 200);

        } catch (\Throwable $e) {

        } catch (\Throwable $e) {
            Log::error('Erreur getOnlineStagiaires: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des stagiaires en ligne',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déconnecter un ou plusieurs stagiaires
     */
    public function disconnectStagiaires(Request $request)
    {
        try {
            $formateur = $request->user();
            
            // Vérification du rôle
            if (!in_array($formateur->role, ['formateur', 'formatrice'])) {
                return response()->json([
                    'error' => 'Accès refusé - Rôle formateur requis'
                ], 403);
            }

            $request->validate([
                'stagiaire_ids' => 'required|array',
                'stagiaire_ids.*' => 'integer|exists:stagiaires,id'
            ]);

            $stagiaireIds = $request->stagiaire_ids;
            
            // Récupérer les user_ids des stagiaires
            $userIds = Stagiaire::whereIn('id', $stagiaireIds)->pluck('user_id')->toArray();
            
            // Mettre is_online à 0 pour ces utilisateurs
            $updated = User::whereIn('id', $userIds)->update(['is_online' => 0]);

            return response()->json([
                'success' => true,
                'message' => "$updated stagiaire(s) déconnecté(s)",
                'disconnected_count' => $updated
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur disconnectStagiaires: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la déconnexion des stagiaires',
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
            $user = $request->user();
            
            // Vérification du rôle
            if (!in_array($user->role, ['admin', 'commercial', 'formateur', 'formatrice'])) {
                return response()->json(['error' => 'Accès refusé'], 403);
            }

            $formateurModel = Formateur::where('user_id', $user->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;
            
            // Récupérer les stagiaires (filtrer par formateur si c'est un formateur)
            $query = Stagiaire::with(['user']);
            
            if (in_array($user->role, ['formateur', 'formatrice'])) {
                if (!$formateurId) {
                    return response()->json(['error' => 'Profil formateur non trouvé'], 404);
                }
                $query->whereHas('formateurs', function($q) use ($formateurId) {
                    $q->where('formateurs.id', $formateurId);
                });
            }
            
            $stagiaires = $query->get()
                ->map(function($stagiaire) {
                    return [
                        'id' => $stagiaire->id,
                        'prenom' => $stagiaire->prenom,
                        'nom' => $stagiaire->user?->name ?? '',
                        'email' => $stagiaire->user?->email ?? '',
                        'telephone' => $stagiaire->telephone,
                        'ville' => $stagiaire->ville,
                        'last_login_at' => $stagiaire->user?->last_login_at ?? null,
                        'last_activity_at' => $stagiaire->user?->last_activity_at ?? null,
                        'is_online' => $stagiaire->user?->is_online ?? false,
                        'last_client' => $stagiaire->user?->last_client ?? null,
                        'image' => $stagiaire->user?->image ?? null,
                    ];
                });

            return response()->json([
                'stagiaires' => $stagiaires
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Erreur getStagiaires', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la récupération des stagiaires',
                'message' => $e->getMessage()
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
            $scope = $request->query('scope', 'mine');
            
            $thresholdDate = Carbon::now()->subDays($days);
            $user = $request->user();
            $formateurModel = Formateur::where('user_id', $user->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;

            $query = Stagiaire::with(['user']);

            // Filtre par formateur (Direct + Formations)
            if ($scope === 'mine' && $formateurId) {
                $query->where(function($q) use ($formateurId) {
                    $q->whereHas('formateurs', function($query) use ($formateurId) {
                        $query->where('formateurs.id', $formateurId);
                    });
                    // ->orWhereHas('catalogue_formations', function($query) use ($formateurId) {
                    //     $query->whereHas('formateurs', function($sub) use ($formateurId) {
                    //         $sub->where('formateurs.id', $formateurId);
                    //     });
                    // });
                });
            }

            // Inactivity logic: last_activity_at OR last_login_at < threshold
            $query->whereHas('user', function($q) use ($thresholdDate) {
                $q->where(function($sub) use ($thresholdDate) {
                    $sub->where(function($inner) use ($thresholdDate) {
                        $inner->whereNull('last_activity_at')
                              ->orWhere('last_activity_at', '<', $thresholdDate);
                    })
                    ->where(function($inner) use ($thresholdDate) {
                        $inner->whereNull('last_login_at')
                              ->orWhere('last_login_at', '<', $thresholdDate);
                    });
                });
            });
            
            $inactiveStagiaires = $query->get()->map(function($stagiaire) {
                $lastActivity = $stagiaire->user?->last_activity_at;
                $lastLogin = $stagiaire->user?->last_login_at;
                
                $maxActivity = null;
                if ($lastActivity && $lastLogin) {
                    $maxActivity = Carbon::parse($lastActivity)->gt(Carbon::parse($lastLogin)) ? $lastActivity : $lastLogin;
                } else {
                    $maxActivity = $lastActivity ?? $lastLogin;
                }

                $daysSinceActivity = $maxActivity ? Carbon::parse($maxActivity)->diffInDays(Carbon::now()) : null;
                
                return [
                    'id' => $stagiaire->id,
                    'prenom' => $stagiaire->prenom,
                    'nom' => $stagiaire->user?->name ?? '',
                    'email' => $stagiaire->user?->email ?? '',
                    'last_activity_at' => $maxActivity,
                    'days_since_activity' => $daysSinceActivity,
                    'never_connected' => !$lastLogin,
                    'last_client' => $stagiaire->user?->last_client,
                    'image' => $stagiaire->user?->image ?? $stagiaire->user?->avatar,
                ];
            });

            return response()->json([
                'inactive_stagiaires' => $inactiveStagiaires,
                'count' => $inactiveStagiaires->count(),
                'threshold_days' => $days,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Erreur getInactiveStagiaires', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la récupération des stagiaires inactifs',
                'message' => $e->getMessage()
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
                        'nom' => $stagiaire->user?->name ?? '',
                        'email' => $stagiaire->user?->email ?? '',
                        'created_at' => $stagiaire->created_at,
                        'fcm_token' => ($stagiaire->user?->fcm_token) ? 'Oui' : 'Non',
                    ];
                });

            return response()->json([
                'never_connected' => $neverConnected,
                'count' => $neverConnected->count(),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Erreur getNeverConnected', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la récupération des stagiaires jamais connectés',
                'message' => $e->getMessage()
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
            $lastActivity = $stagiaire->user?->last_activity_at 
                ? Carbon::parse($stagiaire->user->last_activity_at)->diffForHumans()
                : 'Jamais';
            
            return response()->json([
                'stagiaire' => [
                    'id' => $stagiaire->id,
                    'prenom' => $stagiaire->prenom,
                    'nom' => $stagiaire->user?->name ?? 'N/A',
                    'email' => $stagiaire->user?->email ?? 'N/A',
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
                    'last_login' => $stagiaire->user?->last_login_at,
                    'is_online' => $stagiaire->user?->is_online ?? false,
                    'last_client' => $stagiaire->user?->last_client,
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Stagiaire non trouvé'
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Erreur getStagiaireStats', [
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
            $user = $request->user();
            $formateurModel = Formateur::where('user_id', $user->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;

            // Security check: ensure recipients belong to this formateur (if not admin/commercial)
            if (in_array($user->role, ['formateur', 'formatrice'])) {
                if (!$formateurId) return response()->json(['error' => 'Profil formateur non trouvé'], 404);
                
                $ownedCount = DB::table('formateur_stagiaire')
                    ->where('formateur_id', $formateurId)
                    ->whereIn('stagiaire_id', $recipientIds)
                    ->count();
                
                if ($ownedCount !== count(array_unique($recipientIds))) {
                    return response()->json(['error' => 'Certains destinataires ne font pas partie de votre liste.'], 403);
                }
            }

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
                'type' => 'push',
                'subject' => $title,
                'message' => $body,
                'recipient_count' => count($recipientIds),
                'status' => 'sent',
                'created_by' => $request->user()->id,
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
            $user = $request->user();
            $formateurModel = Formateur::where('user_id', $user->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;

            // Security check: ensure recipients belong to this formateur (if not admin/commercial)
            if (in_array($user->role, ['formateur', 'formatrice'])) {
                if (!$formateurId) return response()->json(['error' => 'Profil formateur non trouvé'], 404);
                
                $ownedCount = DB::table('formateur_stagiaire')
                    ->where('formateur_id', $formateurId)
                    ->whereIn('stagiaire_id', $recipientIds)
                    ->count();
                
                if ($ownedCount !== count(array_unique($recipientIds))) {
                    return response()->json(['error' => 'Certains destinataires ne font pas partie de votre liste.'], 403);
                }
            }

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
            DB::table('notification_history')->insert([
                'type' => 'email',
                'subject' => $subject,
                'message' => $message,
                'recipient_count' => count($recipientIds),
                'status' => 'sent',
                'created_by' => $request->user()->id,
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
            $formateur = $request->user();
            $period = $request->query('period', 'all');
            
            // Récupérer l'ID du formateur
            $formateurModel = Formateur::where('user_id', $formateur->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;
            
            // Récupérer tous les stagiaires de la formation avec leurs points
            $query = DB::table('stagiaires')
                ->join('users', 'stagiaires.user_id', '=', 'users.id')
                ->join('stagiaire_catalogue_formations', 'stagiaires.id', '=', 'stagiaire_catalogue_formations.stagiaire_id')
                ->leftJoin('quiz_participations', function($join) use ($period) {
                    $join->on('users.id', '=', 'quiz_participations.user_id');
                    
                    if ($period === 'week') {
                        $join->where('quiz_participations.created_at', '>=', Carbon::now()->subWeek());
                    } elseif ($period === 'month') {
                        $join->where('quiz_participations.created_at', '>=', Carbon::now()->subMonth());
                    }
                })
                ->where('stagiaire_catalogue_formations.catalogue_formation_id', $formationId);
            
            // Filtrer par formateur si disponible
            if ($formateurId) {
                $query->join('formateur_stagiaire', 'stagiaires.id', '=', 'formateur_stagiaire.stagiaire_id')
                      ->where('formateur_stagiaire.formateur_id', $formateurId);
            }
            
            $ranking = $query->select(
                    'stagiaires.id',
                    'stagiaires.prenom',
                    'users.name as nom',
                    'users.email',
                    'users.image',
                    DB::raw('COALESCE(SUM(quiz_participations.score), 0) as total_points'),
                    DB::raw('COUNT(DISTINCT quiz_participations.id) as total_quiz'),
                    DB::raw('COALESCE(AVG(quiz_participations.score), 0) as avg_score')
                )
                ->groupBy('stagiaires.id', 'stagiaires.prenom', 'users.name', 'users.email', 'users.image')
                ->orderBy('total_points', 'desc')
                ->get()
                ->map(function($stagiaire, $index) {
                    return [
                        'rank' => $index + 1,
                        'id' => $stagiaire->id,
                        'prenom' => $stagiaire->prenom,
                        'nom' => $stagiaire->nom,
                        'email' => $stagiaire->email,
                        'image' => $stagiaire->image,
                        'total_points' => (int) $stagiaire->total_points,
                        'total_quiz' => (int) $stagiaire->total_quiz,
                        'avg_score' => round($stagiaire->avg_score, 1),
                    ];
                });

            return response()->json([
                'formation_id' => $formationId,
                'ranking' => $ranking,
                'total_stagiaires' => $ranking->count(),
                'period' => $period,
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
            $formateur = $request->user();
            $period = $request->query('period', 'all');
            
            // Récupérer l'ID du formateur
            $formateurModel = Formateur::where('user_id', $formateur->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;
            
            // Récupérer tous les stagiaires avec leurs points totaux
            $query = DB::table('stagiaires')
                ->join('users', 'stagiaires.user_id', '=', 'users.id')
                ->leftJoin('quiz_participations', function($join) use ($period) {
                    $join->on('users.id', '=', 'quiz_participations.user_id');
                    
                    if ($period === 'week') {
                        $join->where('quiz_participations.created_at', '>=', Carbon::now()->subWeek());
                    } elseif ($period === 'month') {
                        $join->where('quiz_participations.created_at', '>=', Carbon::now()->subMonth());
                    }
                });
            
            // Filtrer par formateur si disponible
            if ($formateurId) {
                $query->join('formateur_stagiaire', 'stagiaires.id', '=', 'formateur_stagiaire.stagiaire_id')
                      ->where('formateur_stagiaire.formateur_id', $formateurId);
            }
            
            $stagiaires = $query->select(
                    'stagiaires.id',
                    'stagiaires.prenom',
                    'users.name as nom',
                    'users.email',
                    'users.image',
                    DB::raw('COALESCE(SUM(quiz_participations.score), 0) as total_points'),
                    DB::raw('COUNT(DISTINCT quiz_participations.id) as total_quiz'),
                    DB::raw('COALESCE(AVG(quiz_participations.score), 0) as avg_score')
                )
                ->groupBy('stagiaires.id', 'stagiaires.prenom', 'users.name', 'users.email', 'users.image')
                ->orderBy('total_points', 'desc')
                ->get()
                ->map(function($stagiaire, $index) {
                    return [
                        'rank' => $index + 1,
                        'id' => $stagiaire->id,
                        'prenom' => $stagiaire->prenom,
                        'nom' => $stagiaire->nom,
                        'email' => $stagiaire->email,
                        'image' => $stagiaire->image,
                        'total_points' => (int) $stagiaire->total_points,
                        'total_quiz' => (int) $stagiaire->total_quiz,
                        'avg_score' => round($stagiaire->avg_score, 1),
                    ];
                });

            return response()->json([
                'ranking' => $stagiaires,
                'total_stagiaires' => $stagiaires->count(),
                'period' => $period,
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
            $user = $request->user();
            $query = DB::table('medias')
                ->where(function($q) {
                    $q->where('type', 'video')
                      ->orWhere('type', 'LIKE', '%video%');
                });

            // Si c'est un formateur, on filtre par ses formations
            if (in_array($user->role, ['formateur', 'formatrice'])) {
                $formateurModel = Formateur::where('user_id', $user->id)->first();
                
                if ($formateurModel) {
                    $formationIds = DB::table('formateur_catalogue_formation')
                        ->join('catalogue_formations', 'formateur_catalogue_formation.catalogue_formation_id', '=', 'catalogue_formations.id')
                        ->where('formateur_catalogue_formation.formateur_id', $formateurModel->id)
                        ->pluck('catalogue_formations.formation_id')
                        ->filter()
                        ->unique()
                        ->toArray();

                    if (!empty($formationIds)) {
                        $query->whereIn('formation_id', $formationIds);
                    } else {
                        // Si aucune formation n'est assignée explicitly via le pivot, 
                        // on peut tenter de voir si le formateur est lié via les stagiaires (fallback)
                        $fallbackFormationIds = DB::table('formateur_stagiaire')
                            ->join('stagiaire_catalogue_formations', 'formateur_stagiaire.stagiaire_id', '=', 'stagiaire_catalogue_formations.stagiaire_id')
                            ->join('catalogue_formations', 'stagiaire_catalogue_formations.catalogue_formation_id', '=', 'catalogue_formations.id')
                            ->where('formateur_stagiaire.formateur_id', $formateurModel->id)
                            ->pluck('catalogue_formations.formation_id')
                            ->filter()
                            ->unique()
                            ->toArray();

                        if (!empty($fallbackFormationIds)) {
                            $query->whereIn('formation_id', $fallbackFormationIds);
                        } else {
                            // Si vraiment rien, on retourne une liste vide pour la sécurité
                            return response()->json([
                                'videos' => [],
                                'total' => 0,
                                'message' => 'Aucune formation assignée trouvée'
                            ], 200);
                        }
                    }
                } else {
                    return response()->json(['error' => 'Profil formateur non trouvé'], 404);
                }
            }

            $videos = $query->select('id', 'titre', 'description', 'url', 'type', 'created_at', 'formation_id')
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
            $user = $request->user();
            $formateurModel = Formateur::where('user_id', $user->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;

            $query = DB::table('catalogue_formations')
                ->select('id', 'titre as nom', 'description', 'created_at');

            // Optionally filter by formateur
            if ($formateurId) {
                $query->join('formateur_catalogue_formation', 'catalogue_formations.id', '=', 'formateur_catalogue_formation.catalogue_formation_id')
                      ->where('formateur_catalogue_formation.formateur_id', $formateurId);
            }

            $formations = $query->get()
                ->map(function($formation) {
                    // Compter les stagiaires par formation
                    $stagiaireCount = DB::table('stagiaire_catalogue_formations')
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

    /**
     * Récupère les tendances (quiz et activité) pour le dashboard formateur
     */
    public function getTrends(Request $request)
    {
        try {
            $user = $request->user();
            $formateurModel = Formateur::where('user_id', $user->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;

            // Date range: last 30 days
            $startDate = Carbon::now()->subDays(30);

            // Subquery for student IDs assigned to this formateur
            $studentUserIds = DB::table('formateur_stagiaire')
                ->join('stagiaires', 'formateur_stagiaire.stagiaire_id', '=', 'stagiaires.id')
                ->where('formateur_stagiaire.formateur_id', $formateurId)
                ->pluck('stagiaires.user_id');

            // Quiz trends
            $quizTrends = DB::table('quiz_participations')
                ->whereIn('user_id', $studentUserIds)
                ->where('completed_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(completed_at) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('AVG(score) as avg_score')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Activity trends (based on user_app_usages or similar if exists)
            $activityTrends = DB::table('login_histories')
                ->whereIn('user_id', $studentUserIds)
                ->where('created_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'quiz_trends' => $quizTrends,
                'activity_trends' => $activityTrends,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getTrends: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur récupération tendances'], 500);
        }
    }

    /**
     * Récupère la performance détaillée des stagiaires du formateur
     */
    public function getStudentsPerformance(Request $request)
    {
        try {
            $user = $request->user();
            $formateurModel = Formateur::where('user_id', $user->id)->first();
            $formateurId = $formateurModel ? $formateurModel->id : null;

            if (!$formateurId) {
                return response()->json([
                    'performance' => [],
                    'rankings' => [
                        'most_quizzes' => [],
                        'most_active' => [],
                    ]
                ]);
            }

            // Get stagiaires linked directly OR via formations
            $studentsQuery = Stagiaire::with(['user'])
                ->where(function($q) use ($formateurId) {
                    $q->whereHas('formateurs', function($query) use ($formateurId) {
                        $query->where('formateurs.id', $formateurId);
                    });
                    // ->orWhereHas('catalogue_formations', function($query) use ($formateurId) {
                    //     $query->whereHas('formateurs', function($sub) use ($formateurId) {
                    //         $sub->where('formateurs.id', $formateurId);
                    //     });
                    // });
                });

            $students = $studentsQuery->get()->map(function($stagiaire) {
                $userId = $stagiaire->user_id;
                
                // Aggregate quiz stats
                $totalQuizzes = DB::table('quiz_participations')
                    ->where('user_id', $userId)
                    ->where('status', 'completed')
                    ->count();
                
                $lastQuizAt = DB::table('quiz_participations')
                    ->where('user_id', $userId)
                    ->whereNotNull('completed_at')
                    ->latest('completed_at')
                    ->value('completed_at');

                $totalLogins = DB::table('login_histories')
                    ->where('user_id', $userId)
                    ->count();

                return [
                    'id' => $stagiaire->id,
                    'name' => trim("{$stagiaire->prenom} " . ($stagiaire->user ? $stagiaire->user->name : '')),
                    'email' => $stagiaire->user ? $stagiaire->user->email : ($stagiaire->email ?? ''),
                    'image' => $stagiaire->user ? ($stagiaire->user->image ?? $stagiaire->user->avatar ?? null) : null,
                    'last_quiz_at' => $lastQuizAt,
                    'total_quizzes' => (int)$totalQuizzes,
                    'total_logins' => (int)$totalLogins,
                ];
            });

            return response()->json([
                'performance' => $students,
                'rankings' => [
                    'most_quizzes' => $students->sortByDesc('total_quizzes')->values()->take(5),
                    'most_active' => $students->sortByDesc('total_logins')->values()->take(5),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getStudentsPerformance: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur récupération performance'], 500);
        }
    }
}
