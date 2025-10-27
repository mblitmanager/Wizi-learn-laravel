<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStagiaireRequest;
use App\Models\CatalogueFormation;
use App\Models\Formation;
use App\Models\Stagiaire;
use App\Models\User;
use App\Jobs\ImportStagiairesJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Response;

class StagiaireController extends Controller
{
    public function import(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');

            // Sauvegarder le fichier uploadé dans storage/app/imports
            $importsDir = storage_path('app/imports');
            if (!File::exists($importsDir)) {
                File::makeDirectory($importsDir, 0755, true);
            }
            $filename = 'import_stagiaires_' . now()->format('Ymd_His') . '.' . $file->getClientOriginalExtension();
            $storedPath = $file->storeAs('imports', $filename);
            $fullPath = storage_path('app/' . $storedPath);

            // Dispatcher le job pour exécuter l'import en background
            ImportStagiairesJob::dispatch($fullPath);

            return redirect()->route('stagiaires.index')
                ->with('success', 'Importation lancée en tâche de fond. Le rapport sera généré dans storage/reports une fois terminée.');
        } catch (\Exception $e) {
            Log::error('Erreur import : ' . $e->getMessage());
            return redirect()->route('stagiaires.index')
                ->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Télécharger un rapport d'import généré en arrière-plan
     * @param string $filename
     */
    public function downloadImportReport($filename)
    {
        // Sécurité : n'autoriser que les fichiers de type attendu
        if (!preg_match('/^import_stagiaires_\d{8}_\d{6}\.txt$/', $filename)) {
            return redirect()->back()->with('error', 'Nom de fichier invalide.');
        }

        $reportPath = storage_path('reports' . DIRECTORY_SEPARATOR . $filename);
        if (!File::exists($reportPath)) {
            return redirect()->back()->with('error', 'Le rapport demandé est introuvable.');
        }

        return Response::download($reportPath, $filename);
    }


    public function downloadStagiaireModel()
    {
        $filePath = public_path('models/stagiaire/stagiaire.xlsx');

        if (!File::exists($filePath)) {
            return redirect()->back()->with('error', 'Le fichier modèle est introuvable.');
        }

        $fileName = 'modele_import_stagiaire.xlsx';

        return Response::download($filePath, $fileName);
    }
}
