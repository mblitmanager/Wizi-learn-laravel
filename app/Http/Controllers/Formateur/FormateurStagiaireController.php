<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FormateurStagiaireController extends Controller
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
     * Liste de tous les stagiaires du formateur
     */
    public function tousLesStagiaires()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        // Récupérer tous les stagiaires
        $tousStagiaires = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->with(['user', 'catalogue_formations'])
            ->orderBy('date_debut_formation', 'desc')
            ->paginate(20);

        // Calculer les statistiques pour l'affichage
        $stats = $this->getStats($formateur);

        return view('formateur.stagiaires.index', compact('tousStagiaires', 'stats'));
    }

    /**
     * Liste des stagiaires en cours de formation (ACTUELLEMENT)
     */
    public function stagiairesEnCours()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $stagiairesEnCours = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->where(function ($query) {
                $query->where('statut', 1)
                    ->orWhereNull('statut');
            })
            // IMPORTANT : date début DANS LE PASSÉ et date fin DANS LE FUTUR
            ->where('date_debut_formation', '<=', now())
            ->where(function ($query) {
                $query->where('date_fin_formation', '>', now())
                    ->orWhereNull('date_fin_formation');
            })
            ->with(['user', 'catalogue_formations'])
            ->orderBy('date_debut_formation', 'desc')
            ->paginate(15);

        return view('formateur.stagiaires.en-cours', compact('stagiairesEnCours'));
    }

    /**
     * NOUVEAU : Stagiaires avec formation à venir
     */
    public function stagiairesAVenir()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $stagiairesAVenir = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->where('date_debut_formation', '>', now()) // Date début dans le FUTUR
            ->with(['user', 'catalogue_formations'])
            ->orderBy('date_debut_formation', 'asc')
            ->paginate(15);

        return view('formateur.stagiaires.a-venir', compact('stagiairesAVenir'));
    }


    /**
     * Stagiaires ayant terminé leur formation
     */
    public function stagiairesTerminesRecent()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $stagiairesTermines = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->where('date_fin_formation', '<=', now())
            ->with(['user', 'catalogue_formations'])
            ->orderBy('date_fin_formation', 'desc')
            ->paginate(15);

        return view('formateur.stagiaires.termines', compact('stagiairesTermines'));
    }

    // Dans FormateurStagiaireController.php

    public function show($id)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $stagiaire = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->with([
                'user',
                'catalogue_formations',
                'formateurs.user',
                'progressions',
                'watchedVideos',
                'classements.quiz', // AJOUTER cette ligne
                'quizParticipations.quiz' // AJOUTER cette ligne
            ])->findOrFail($id);

        // AJOUTER le calcul des statistiques comme dans FormateurClassementController
        $statistiques = $this->calculerStatistiquesStagiaire($stagiaire);

        return view('formateur.stagiaires.show', compact('stagiaire', 'statistiques'));
    }

    /**
     * Nouvelle méthode pour calculer les statistiques du stagiaire
     */
    private function calculerStatistiquesStagiaire($stagiaire)
    {
        // Calculer le total des points depuis les classements
        $totalPoints = $stagiaire->classements->sum('points');

        // Quiz complétés (avec status 'completed')
        $quizCompletes = $stagiaire->quizParticipations->where('status', 'completed');

        // Calcul de la progression
        $progressionMoyenne = 0;
        if ($quizCompletes->isNotEmpty()) {
            $totalScorePossible = $quizCompletes->sum(function ($participation) {
                return $participation->quiz->nb_points_total ?? 100;
            });

            $totalScoreObtenu = $quizCompletes->sum('score');

            if ($totalScorePossible > 0) {
                $progressionMoyenne = min(100, ($totalScoreObtenu / $totalScorePossible) * 100);
            }
        }

        // Calcul du temps total passé
        $tempsProgressions = $stagiaire->progressions->sum('time_spent') ?? 0;
        $tempsQuiz = $stagiaire->quizParticipations->sum('time_spent') ?? 0;

        return [
            'total_points' => $totalPoints,
            'quiz_completes' => $quizCompletes->count(),
            'quiz_total_participations' => $stagiaire->quizParticipations->count(),
            'meilleur_rang' => $stagiaire->classements->min('rang') ?? 'N/A',
            'videos_regardees' => $stagiaire->watchedVideos->count(),
            'progression_moyenne' => round($progressionMoyenne, 2),
            'temps_total_passe' => $tempsProgressions + $tempsQuiz,
            'participations_quiz' => $stagiaire->quizParticipations->count(),
            'derniere_activite' => $stagiaire->derniere_activite,
            'score_total' => $quizCompletes->sum('score'),
            'score_max_possible' => $quizCompletes->sum(function ($participation) {
                return $participation->quiz->nb_points_total ?? 100;
            }),
            'moyenne_score' => $quizCompletes->isNotEmpty() ? round($quizCompletes->avg('score'), 2) : 0,
        ];
    }


    /**
     * Récupérer les statistiques des stagiaires
     */
    private function getStats($formateur)
    {
        $stagiairesEnCours = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->where(function ($query) {
                $query->where('statut', 1)
                    ->orWhereNull('statut');
            })
            ->where(function ($query) {
                $query->where('date_debut_formation', '<=', now())
                    ->where(function ($subQuery) {
                        $subQuery->where('date_fin_formation', '>', now())
                            ->orWhereNull('date_fin_formation');
                    });
            })
            ->count();
        $stagiairesTermines = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->where(function ($query) {
                $query->where('statut', 0)
                    ->orWhereNull('statut');
            })
            ->where('date_fin_formation', '<=', now())
            ->count();

        return [
            'stagiairesEnCours' => $stagiairesEnCours,
            'stagiairesTermines' => $stagiairesTermines
        ];
    }

    /**
     * API: Get complete student profile for mobile app
     * GET /formateur/stagiaire/{id}/profile
     */
    public function getProfileApi($id)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $stagiaire = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->with([
                'user',
                'catalogue_formations',
                'progressions',
                'watchedVideos',
                'classements.quiz',
                'quizParticipations.quiz'
            ])
            ->findOrFail($id);

        // Calculate statistics
        $stats = $this->calculerStatistiquesStagiaire($stagiaire);

        // Get quiz history with details
        $quizHistory = $stagiaire->quizParticipations
            ->where('status', 'completed')
            ->map(function ($participation) {
                return [
                    'quiz_id' => $participation->quiz_id,
                    'title' => $participation->quiz->nom ?? 'Quiz',
                    'category' => $participation->quiz->categorie ?? 'Général',
                    'score' => $participation->score ?? 0,
                    'max_score' => $participation->quiz->nb_points_total ?? 100,
                    'completed_at' => $participation->completed_at ?? $participation->updated_at,
                    'time_spent' => $participation->time_spent ?? 0,
                ];
            })
            ->sortByDesc('completed_at')
            ->values();

        // Get formations with progress
        $formations = $stagiaire->catalogue_formations->map(function ($formation) use ($stagiaire) {
            // Calculate progress based on watched videos
            $totalVideos = $formation->medias->where('type', 'video')->count();
            $watchedCount = $stagiaire->watchedVideos
                ->whereIn('media_id', $formation->medias->pluck('id'))
                ->count();
            
            $progress = $totalVideos > 0 ? round(($watchedCount / $totalVideos) * 100) : 0;

            return [
                'id' => $formation->id,
                'title' => $formation->titre,
                'category' => $formation->categorie ?? 'Général',
                'started_at' => $stagiaire->date_debut_formation,
                'completed_at' => $progress === 100 ? $stagiaire->date_fin_formation : null,
                'progress' => $progress,
            ];
        })->values();

        // Recent activities (last 30 days)
        $recentActivities = collect();
        
        // Add quiz completions
        $stagiaire->quizParticipations
            ->where('status', 'completed')
            ->sortByDesc('completed_at')
            ->take(10)
            ->each(function ($participation) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'quiz_completed',
                    'title' => $participation->quiz->nom ?? 'Quiz',
                    'score' => $participation->score,
                    'timestamp' => $participation->completed_at ?? $participation->updated_at,
                ]);
            });

        // Add video watch events
        $stagiaire->watchedVideos
            ->sortByDesc('watched_at')
            ->take(5)
            ->each(function ($watched) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'video_watched',
                    'title' => $watched->media->titre ?? 'Vidéo',
                    'score' => null,
                    'timestamp' => $watched->watched_at,
                ]);
            });

        // Sort all activities by timestamp
        $recentActivities = $recentActivities
            ->sortByDesc('timestamp')
            ->take(15)
            ->values();

        // Activity calendar (last 30 days)
        $last30Days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $actions = 0;

            // Count quiz activities for this day
            $actions += $stagiaire->quizParticipations
                ->filter(function ($participation) use ($date) {
                    return Carbon::parse($participation->created_at)->format('Y-m-d') === $date;
                })
                ->count();

            // Count watched videos for this day
            $actions += $stagiaire->watchedVideos
                ->filter(function ($watched) use ($date) {
                    return Carbon::parse($watched->watched_at)->format('Y-m-d') === $date;
                })
                ->count();

            $last30Days->push([
                'date' => $date,
                'actions' => $actions,
            ]);
        }

        // Determine current badge
        $totalPoints = $stats['total_points'];
        $currentBadge = 'Aucun';
        if ($totalPoints >= 1000) $currentBadge = 'Platine';
        elseif ($totalPoints >= 500) $currentBadge = 'Or';
        elseif ($totalPoints >= 200) $currentBadge = 'Argent';
        elseif ($totalPoints >= 50) $currentBadge = 'Bronze';

        return response()->json([
            'stagiaire' => [
                'id' => $stagiaire->id,
                'prenom' => $stagiaire->user->prenom ?? '',
                'nom' => $stagiaire->user->nom ?? '',
                'email' => $stagiaire->user->email ?? '',
                'image' => $stagiaire->user->image ?? null,
                'created_at' => $stagiaire->created_at,
                'last_login' => $stagiaire->derniere_activite,
            ],
            'stats' => [
                'total_points' => $totalPoints,
                'current_badge' => $currentBadge,
                'formations_completed' => $formations->where('progress', 100)->count(),
                'formations_in_progress' => $formations->where('progress', '>', 0)->where('progress', '<', 100)->count(),
                'quizzes_completed' => $stats['quiz_completes'],
                'average_score' => $stats['moyenne_score'],
                'total_time_minutes' => round($stats['temps_total_passe'] / 60),
                'login_streak' => 0, // TODO: Implement streak calculation
            ],
            'activity' => [
                'last_30_days' => $last30Days,
                'recent_activities' => $recentActivities,
            ],
            'formations' => $formations,
            'quiz_history' => $quizHistory,
        ]);
    }

    /**
     * API: Get trainer notes for a student
     * GET /formateur/stagiaire/{id}/notes
     */
    public function getNotesApi($id)
    {
        $this->checkFormateur();
        // TODO: Implement notes table and model
        
        return response()->json([
            'notes' => []
        ]);
    }

    /**
     * API: Add a trainer note for a student
     * POST /formateur/stagiaire/{id}/note
     */
    public function addNoteApi(Request $request, $id)
    {
        $this->checkFormateur();
        
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // TODO: Implement notes table and model
        
        return response()->json([
            'success' => true,
            'message' => 'Note ajoutée avec succès'
        ]);
    }
}