<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeInscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DemandeHistoriqueController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DemandeInscription::with(['parrain', 'filleul', 'formation'])
                ->orderBy('created_at', 'desc');

            // Filtrage par type de demande
            if ($request->has('type') && $request->type !== '') {
                $filterValue = $this->getFilterValue($request->type);
                $query->where('motif', $filterValue);
            }

            // Filtrage par statut
            if ($request->has('statut') && $request->statut !== '') {
                $query->where('statut', $request->statut);
            }

            $demandes = $query->paginate(15);

            return view('admin.demandes.index', [
                'demandes' => $demandes,
                'filters' => $request->only(['type', 'statut'])
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors du chargement des demandes: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $demande = DemandeInscription::with(['parrain', 'filleul', 'formation'])
                ->findOrFail($id);

            return view('admin.demandes.show', compact('demande'));
        } catch (\Exception $e) {
            return redirect()->route('admin.demande.historique.index')
                ->with('error', 'Demande non trouvée ou erreur de chargement.');
        }
    }

    /**
     * Export CSV des demandes
     */
    public function exportCSV(Request $request)
    {
        try {
            $filters = $request->only(['type', 'statut']);
            $demandes = $this->getDemandesForExport($filters);

            $fileName = 'demandes_inscription_' . date('Y-m-d_H-i') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function () use ($demandes) {
                $file = fopen('php://output', 'w');

                // Ajouter BOM pour Excel et l'encodage UTF-8
                fwrite($file, "\xEF\xBB\xBF");

                // En-tête CSV
                fputcsv($file, [
                    'ID',
                    'Type de demande',
                    'Formation',
                    'Parrain',
                    'Filleul',
                    'Email Filleul',
                    'Téléphone Filleul',
                    'Date de demande',
                    'Statut',
                    'Date de création'
                ], ';');

                // Données
                foreach ($demandes as $demande) {
                    fputcsv($file, [
                        $demande->id,
                        $this->getTypeLabel($demande->motif),
                        $demande->formation->titre ?? 'N/A',
                        $demande->parrain ? $demande->parrain->name : 'N/A',
                        $demande->filleul->name ?? 'N/A',
                        $demande->filleul->email ?? 'N/A',
                        $demande->filleul->telephone ?? 'N/A',
                        $demande->date_demande ? $demande->date_demande->format('d/m/Y H:i') : 'N/A',
                        $this->getStatutLabel($demande->statut),
                        $demande->created_at->format('d/m/Y H:i')
                    ], ';');
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les demandes pour l'export
     */
    private function getDemandesForExport(array $filters = [])
    {
        $query = DemandeInscription::with(['parrain', 'filleul', 'formation'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['type'])) {
            $filterValue = $this->getFilterValue($filters['type']);
            $query->where('motif', $filterValue);
        }

        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        return $query->get();
    }

    /**
     * Convertit le type de filtre en valeur de motif
     */
    private function getFilterValue($type): string
    {
        return $type === 'demande_inscription_parrainage'
            ? 'Soumission d\'une demande d\'inscription par parrainage'
            : 'Demande d\'inscription à une formation';
    }

    /**
     * Convertit le statut en libellé lisible
     */
    private function getStatutLabel($statut): string
    {
        $statuts = [
            'en_attente' => 'En attente',
            'complete' => 'Complète',
            'annulee' => 'Annulée',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée'
        ];

        return $statuts[$statut] ?? ucfirst($statut);
    }

    /**
     * Convertit le motif en type lisible
     */
    private function getTypeLabel($motif): string
    {
        return $motif === 'Soumission d\'une demande d\'inscription par parrainage'
            ? 'Parrainage'
            : 'Formation';
    }
}
