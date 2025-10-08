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
        if ($user->role !== 'formateur') {
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

    /**
     * Détails d'un stagiaire
     */
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
                'watchedVideos'
            ])->findOrFail($id);

        return view('formateur.stagiaires.show', compact('stagiaire'));
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
                $query->where('statut', 'actif')
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

        $dateLimite = Carbon::now()->subYear();
        $stagiairesTermines = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->where(function ($query) {
                $query->where('statut', 'inactif')
                      ->orWhere('statut', 'terminé');
            })
            ->where('date_fin_formation', '<=', now())
            ->where('date_fin_formation', '>=', $dateLimite)
            ->count();

        return [
            'stagiairesEnCours' => $stagiairesEnCours,
            'stagiairesTermines' => $stagiairesTermines
        ];
    }
}