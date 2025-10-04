<?php
// app/Http/Controllers/Formateur/FormateurStagiaireController.php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FormateurStagiaireController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Formateur']);
    }

    /**
     * Liste de tous les stagiaires du formateur
     */
    public function tousLesStagiaires()
    {
        $formateur = Auth::user()->formateur;

        $tousStagiaires = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->with(['user', 'catalogue_formations'])
            ->orderBy('date_debut_formation', 'desc')
            ->paginate(20);

        return view('formateur.stagiaires.index', compact('tousStagiaires'));
    }

    /**
     * Liste des stagiaires en cours de formation
     */
    public function stagiairesEnCours()
    {
        $formateur = Auth::user()->formateur;

        $stagiairesEnCours = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->where('statut', 'actif')
            ->where(function ($query) {
                $query->whereNull('date_fin_formation')
                    ->orWhere('date_fin_formation', '>', now());
            })
            ->where('date_debut_formation', '<=', now())
            ->with(['user', 'catalogue_formations'])
            ->orderBy('date_debut_formation', 'desc')
            ->paginate(15);

        return view('formateur.stagiaires.en-cours', compact('stagiairesEnCours'));
    }

    /**
     * Liste des stagiaires ayant terminé leur formation depuis moins d'un an
     */
    public function stagiairesTerminesRecent()
    {
        $formateur = Auth::user()->formateur;
        $dateLimite = Carbon::now()->subYear();

        $stagiairesTermines = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->where('statut', 'inactif')
            ->where('date_fin_formation', '>=', $dateLimite)
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
        $formateur = Auth::user()->formateur;

        $stagiaire = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->with([
                'user',
                'catalogue_formations',
                'formateurs',
                'progressions',
                'watchedVideos'
            ])->findOrFail($id);

        return view('formateur.stagiaires.show', compact('stagiaire'));
    }
}