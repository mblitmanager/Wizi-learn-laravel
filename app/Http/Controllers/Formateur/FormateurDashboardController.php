<?php
namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\CatalogueFormation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FormateurDashboardController extends Controller
{
    public function index()
    {
        $formateur = Auth::user()->formateur;

        // Statistiques pour le formateur
        $stats = [
            'total_stagiaires' => $formateur->stagiaires()->count(),
            'stagiaires_en_cours' => $formateur->stagiaires()
                ->where('statut', 'actif')
                ->where(function ($query) {
                    $query->whereNull('date_fin_formation')
                        ->orWhere('date_fin_formation', '>', now());
                })
                ->count(),
            'stagiaires_termines' => $formateur->stagiaires()
                ->where('statut', 'inactif')
                ->where('date_fin_formation', '>=', Carbon::now()->subYear())
                ->count(),
            'formations_encadrees' => $formateur->catalogue_formations()->count()
        ];

        // Derniers stagiaires ajoutés
        $recentStagiaires = $formateur->stagiaires()
            ->with(['user', 'catalogue_formations'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('formateur.dashboard', compact('stats', 'recentStagiaires'));
    }
}