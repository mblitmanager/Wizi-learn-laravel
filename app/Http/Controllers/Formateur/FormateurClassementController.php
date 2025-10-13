<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Classement;
use App\Models\QuizParticipation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormateurClassementController extends Controller
{
    /**
     * Vérifier que l'utilisateur est un formateur
     */
    private function checkFormateur()
    {
        $user = Auth::user();
        if ($user->role !== 'formateur') {
            abort(403, 'Accès réservé aux formateurs.');
        }

        if (!$user->formateur) {
            abort(404, 'Profil formateur non trouvé.');
        }
    }

    /**
     * Classement général des stagiaires
     */
    public function classementGeneral()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        // Récupérer les stagiaires avec leurs classements et points totaux
        $stagiairesAvecClassement = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->with(['user', 'classements.quiz', 'progressions', 'quizParticipations', 'watchedVideos'])
            ->get()
            ->map(function ($stagiaire) {
                // Calculer le total des points de tous les classements
                $totalPoints = $stagiaire->classements->sum('points');

                // Calculer le meilleur rang
                $meilleurRang = $stagiaire->classements->min('rang') ?? null;

                // Nombre de quiz complétés
                $quizCompletes = $stagiaire->classements->count();

                return [
                    'stagiaire' => $stagiaire,
                    'total_points' => $totalPoints,
                    'meilleur_rang' => $meilleurRang,
                    'quiz_completes' => $quizCompletes,
                    'a_utilise_app' => $stagiaire->a_utilise_application // Utilise l'accesseur
                ];
            })
            ->sortByDesc('total_points')
            ->values();

        // Ajouter le rang global
        $classementAvecRang = $stagiairesAvecClassement->map(function ($item, $index) {
            $item['rang_global'] = $index + 1;
            return $item;
        });

        return view('formateur.stagiaires.classement', compact('classementAvecRang'));
    }

    /**
     * Stagiaires ayant utilisé l'application
     */
    public function stagiairesAvecApplication()
{
    $this->checkFormateur();
    $formateur = Auth::user()->formateur;

    $stagiairesAvecApp = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
        $query->where('formateur_id', $formateur->id);
    })
    ->where(function ($query) {
        $query->whereHas('quizParticipations')
            ->orWhereHas('progressions')
            ->orWhereHas('watchedVideos')
            ->orWhereHas('classements');
    })
    ->with([
        'user',
        'classements.quiz',
        'quizParticipations.quiz', // Important: charger le quiz pour avoir nb_points_total
        'progressions',
        'watchedVideos'
    ])
    ->orderBy('prenom')
    ->paginate(20);

    // Statistiques d'utilisation
    $stats = [
        'total_avec_app' => $stagiairesAvecApp->total(),
        'quiz_completes' => 0,
        'videos_regardees' => 0,
        'progression_moyenne' => 0,
        'stagiaires_avec_progression' => 0
    ];

    $totalProgression = 0;
    $stagiairesAvecProgression = 0;

    foreach ($stagiairesAvecApp as $stagiaire) {
        // Quiz complétés (avec status 'completed')
        $quizCompletes = $stagiaire->quizParticipations->where('status', 'completed');
        $stats['quiz_completes'] += $quizCompletes->count();
        $stats['videos_regardees'] += $stagiaire->watchedVideos->count();
        
        // CALCUL DE LA PROGRESSION - Méthode recommandée
        if ($quizCompletes->isNotEmpty()) {
            $totalScorePossible = $quizCompletes->sum(function ($participation) {
                return $participation->quiz->nb_points_total ?? 100;
            });
            
            $totalScoreObtenu = $quizCompletes->sum('score');
            
            if ($totalScorePossible > 0) {
                $progressionStagiaire = min(100, ($totalScoreObtenu / $totalScorePossible) * 100);
                $totalProgression += $progressionStagiaire;
                $stagiairesAvecProgression++;
                
                // Stocker la progression pour la vue
                $stagiaire->progression_calculee = $progressionStagiaire;
            }
        } else {
            $stagiaire->progression_calculee = 0;
        }
    }

    // Calcul de la progression moyenne globale
    if ($stagiairesAvecProgression > 0) {
        $stats['progression_moyenne'] = round($totalProgression / $stagiairesAvecProgression, 2);
        $stats['stagiaires_avec_progression'] = $stagiairesAvecProgression;
    }

    return view('formateur.stagiaires.application', compact('stagiairesAvecApp', 'stats'));
}
    /**
     * Détails du classement d'un stagiaire spécifique
     */
    public function detailsClassement($id)
{
    $this->checkFormateur();
    $formateur = Auth::user()->formateur;

    $stagiaire = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
        $query->where('formateur_id', $formateur->id);
    })
    ->with([
        'user',
        'classements.quiz.formation',
        'progressions.quiz',
        'quizParticipations.quiz', // Charger le quiz pour avoir nb_points_total
        'watchedVideos'
    ])
    ->findOrFail($id);

    // CALCUL DE LA PROGRESSION CORRIGÉ
    $quizCompletes = $stagiaire->quizParticipations->where('status', 'completed');
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

    // Calcul du temps total passé - version sécurisée
    $tempsProgressions = $stagiaire->progressions->sum('time_spent') ?? 0;
    $tempsQuiz = $stagiaire->quizParticipations->sum('time_spent') ?? 0;

    $statistiques = [
        'total_points' => $stagiaire->classements->sum('points') ?? 0,
        'quiz_completes' => $quizCompletes->count(), // Utiliser les quiz complétés plutôt que les classements
        'quiz_total_participations' => $stagiaire->quizParticipations->count(),
        'meilleur_rang' => $stagiaire->classements->min('rang') ?? 'N/A',
        'videos_regardees' => $stagiaire->watchedVideos->count(),
        'progression_moyenne' => round($progressionMoyenne, 2), // Utiliser la progression calculée
        'temps_total_passe' => $tempsProgressions + $tempsQuiz,
        'participations_quiz' => $stagiaire->quizParticipations->count(),
        'derniere_activite' => $stagiaire->derniere_activite,
        
        // Statistiques supplémentaires pour plus de détails
        'score_total' => $quizCompletes->sum('score'),
        'score_max_possible' => $quizCompletes->sum(function ($participation) {
            return $participation->quiz->nb_points_total ?? 100;
        }),
        'moyenne_score' => $quizCompletes->isNotEmpty() ? round($quizCompletes->avg('score'), 2) : 0,
        'temps_moyen_quiz' => $quizCompletes->isNotEmpty() ? round($quizCompletes->avg('time_spent') / 60, 2) : 0 // en minutes
    ];

    return view('formateur.stagiaires.details-classement', compact('stagiaire', 'statistiques'));
}

    /**
     * API pour le classement (format JSON)
     */
    public function apiClassement()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $classement = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->with(['user', 'classements.quiz.formation', 'quizParticipations', 'progressions', 'watchedVideos'])
            ->get()
            ->map(function ($stagiaire) {
                return [
                    'id' => $stagiaire->id,
                    'prenom' => $stagiaire->prenom,
                    'civilite' => $stagiaire->civilite,
                    'email' => $stagiaire->user->email ?? null,
                    'total_points' => $stagiaire->classements->sum('points'),
                    'meilleur_rang' => $stagiaire->classements->min('rang'),
                    'nombre_quiz' => $stagiaire->classements->count(),
                    'classements' => $stagiaire->classements->map(function ($classement) {
                        return [
                            'rang' => $classement->rang,
                            'points' => $classement->points,
                            'quiz' => $classement->quiz->titre ?? 'Quiz inconnu',
                            'formation' => $classement->quiz->formation->titre ?? 'Formation inconnue'
                        ];
                    }),
                    'a_utilise_application' => $stagiaire->a_utilise_application // Utilise l'accesseur
                ];
            })
            ->sortByDesc('total_points')
            ->values()
            ->map(function ($item, $index) {
                $item['rang_global'] = $index + 1;
                return $item;
            });

        return response()->json([
            'classement' => $classement,
            'mise_a_jour' => now()->toDateTimeString()
        ]);
    }
}
