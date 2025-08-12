<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeInscription;
use Illuminate\Http\Request;

class DemandeHistoriqueController extends Controller
{
    public function index(Request $request)
    {
        $query = DemandeInscription::with(['parrain', 'filleul', 'formation'])
            ->orderBy('created_at', 'desc');

        // Filtrage par type de demande
        if ($request->has('type')) {
            $filterValue = $request->type === 'demande_inscription_parrainage'
                ? 'Soumission d\'une demande d\'inscription par parrainage'
                : 'Demande d\'inscription à une formation';

            $query->where('motif', $filterValue);
        }

        // Filtrage par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $demandes = $query->paginate(15);

        return view('admin.demandes.index', [
            'demandes' => $demandes,
            'filters' => $request->only(['type', 'statut'])
        ]);
    }

    // Ajoutez la méthode show manquante
    public function show($id)
    {
        $demande = DemandeInscription::with(['parrain', 'filleul', 'formation'])
            ->findOrFail($id);

        return view('admin.demandes.show', compact('demande'));
    }
}
