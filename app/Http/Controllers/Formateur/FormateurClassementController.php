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
        if ($user->role !== 'formateur' && $user->role !== 'formatrice') {
            abort(403, 'Accès réservé aux formateurs.');
        }

        if (!$user->formateur) {
            abort(404, 'Profil formateur non trouvé.');
        }
    }

    public function classementGeneral()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        // Récupérer les stagiaires avec leurs classements
        $stagiairesAvecClassement = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->with(['user', 'classements.quiz'])
            ->get()
            ->map(function ($stagiaire) {
                $totalPoints = $stagiaire->classements->sum('points');
                $quizAvecClassement = $stagiaire->classements->count();
                $meilleurRang = $stagiaire->classements->min('rang') ?? null;

                return [
                    'stagiaire' => $stagiaire,
                    'total_points' => $totalPoints,
                    'meilleur_rang' => $meilleurRang,
                    'quiz_completes' => $quizAvecClassement,
                    'a_utilise_app' => $stagiaire->a_utilise_application
                ];
            })
            ->filter(function ($item) {
                return $item['quiz_completes'] > 0;
            })
            ->sortByDesc('total_points')
            ->values();

        // CORRECTION : Calcul SIMPLE et CORRECT des rangs
        $classementAvecRang = $this->calculerRangsDefinitif($stagiairesAvecClassement);

        return view('formateur.stagiaires.classement', compact('classementAvecRang'));
    }

    /**
     * MÉTHODE CORRIGÉE : Calcul des rangs avec ex-aequo
     */
    private function calculerRangsDefinitif($stagiaires)
    {
        if ($stagiaires->isEmpty()) {
            return collect();
        }

        $classement = collect();
        $rang = 1;
        $previousPoints = $stagiaires[0]['total_points'];

        foreach ($stagiaires as $index => $item) {
            // Si les points sont différents du précédent, le rang = position + 1
            if ($index > 0 && $item['total_points'] != $previousPoints) {
                $rang = $index + 1;
            }

            $item['rang_global'] = $rang;
            $classement->push($item);

            $previousPoints = $item['total_points'];
        }

        return $classement;
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
}
