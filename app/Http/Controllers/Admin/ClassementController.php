<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use App\Models\Classement;
use App\Models\CatalogueFormation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClassementController extends Controller
{
    // Page d'accueil listant tous les partenaires
    public function index()
    {
        $partenaires = Partenaire::withCount('stagiaires')
            ->orderBy('identifiant')
            ->get();

        return view('admin.classements.index', compact('partenaires'));
    }

    // Page des classements pour un partenaire spécifique
    public function show($partenaireId, Request $request)
    {
        $partenaire = Partenaire::with(['stagiaires'])->findOrFail($partenaireId);

        // Récupérer les filtres
        $periode = $request->input('periode', 'global');
        $formationId = $request->input('formation_id', null);

        // Base query pour les classements
        $query = Classement::whereIn('stagiaire_id', $partenaire->stagiaires->pluck('id'))
            ->with(['stagiaire.user', 'quiz.formation']);

        // Filtrer par formation si spécifié
        if ($formationId) {
            $query->whereHas('quiz.formation', function ($q) use ($formationId) {
                $q->where('formations.id', $formationId); // Spécifier la table
            });
        }

        // Filtrer par période
        if ($periode !== 'global') {
            $dateRange = $this->getDateRange($periode);
            $query->whereBetween('created_at', $dateRange);
        }

        // Récupérer les classements groupés par stagiaire
        $classements = $query->get()
            ->groupBy('stagiaire_id')
            ->map(function ($items) {
                return [
                    'stagiaire' => $items->first()->stagiaire,
                    'total_points' => $items->sum('points'),
                    'classements' => $items
                ];
            })
            ->sortByDesc('total_points')
            ->values()
            ->map(function ($item, $index) {
                $item['rang'] = $index + 1;
                return $item;
            });

        // Récupérer les formations disponibles pour le filtre - VERSION CORRIGÉE
        $formations = CatalogueFormation::whereHas('stagiaires', function ($q) use ($partenaire) {
            $q->whereIn('stagiaires.id', $partenaire->stagiaires->pluck('id')); // Spécifier la table
        })->get();

        return view('admin.classements.show', compact('partenaire', 'classements', 'formations', 'periode', 'formationId'));
    }

    protected function getDateRange($periode)
    {
        $now = Carbon::now();

        switch ($periode) {
            case 'jour':
                return [$now->startOfDay(), $now->copy()->endOfDay()];
            case 'semaine':
                return [$now->startOfWeek(), $now->copy()->endOfWeek()];
            case 'mois':
                return [$now->startOfMonth(), $now->copy()->endOfMonth()];
            default:
                return [null, null];
        }
    }
}
