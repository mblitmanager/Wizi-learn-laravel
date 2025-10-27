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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Response;
use App\Models\ImportJob;

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

            // create an ImportJob record so UI can show queued/running status
            $importJob = \App\Models\ImportJob::create([
                'status' => 'queued',
                'started_at' => null,
                'finished_at' => null,
            ]);

            // Dispatcher le job pour exécuter l'import en background en lui passant l'id
            ImportStagiairesJob::dispatch($fullPath, $importJob->id);

            return redirect()->route('stagiaires.index')
                ->with('success', 'Importation lancée en tâche de fond. Le rapport sera généré dans storage/reports une fois terminée.');
        } catch (\Exception $e) {
            Log::error('Erreur import : ' . $e->getMessage());
            return redirect()->route('stagiaires.index')
                ->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la liste des stagiaires (page d'administration)
     */
    public function index(Request $request)
    {
        // Charger les stagiaires avec leur user lié
        $stagiaires = Stagiaire::with('user')->orderBy('id', 'desc')->get();

    $lastJob = ImportJob::orderBy('created_at', 'desc')->first();
    $jobRunning = $lastJob ? in_array($lastJob->status, ['queued', 'running']) : false;
    $lastReport = $lastJob ? $lastJob->report_filename : null;

        return view('admin.stagiaires.index', compact('stagiaires', 'jobRunning', 'lastReport'));
    }

    /**
     * Désactiver un stagiaire
     */
    public function desactive($id)
    {
        $stagiaire = Stagiaire::findOrFail($id);
        $stagiaire->statut = 0;
        $stagiaire->save();

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire désactivé.');
    }

    /**
     * Activer un stagiaire
     */
    public function active($id)
    {
        $stagiaire = Stagiaire::findOrFail($id);
        $stagiaire->statut = 1;
        $stagiaire->save();

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire activé.');
    }

    /**
     * Liste les rapports d'import récents pour téléchargement
     */
    public function reports()
    {
        $reportsDir = storage_path('reports');
        $files = [];

        if (File::exists($reportsDir)) {
            // collect files matching pattern
            $col = collect(File::files($reportsDir))
                ->filter(function ($f) {
                    return preg_match('/^import_stagiaires_\d{8}_\d{6}\.txt$/', $f->getFilename());
                })
                ->map(function ($f) {
                    return [
                        'path' => $f->getPathname(),
                        'filename' => $f->getFilename(),
                        'size' => $f->getSize(),
                        'modified_ts' => $f->getMTime(),
                        'modified' => date('Y-m-d H:i:s', $f->getMTime()),
                        'url' => route('stagiaires.import.report', $f->getFilename()),
                    ];
                })
                ->sortByDesc('modified_ts')
                ->values();

            // Purge older reports keeping only the most recent N files
            $keep = 50; // configurable if needed
            if ($col->count() > $keep) {
                $toDelete = $col->slice($keep);
                foreach ($toDelete as $d) {
                    try {
                        File::delete($d['path']);
                    } catch (\Exception $e) {
                        Log::warning('Impossible de supprimer l\'ancien rapport: ' . $d['filename'] . ' - ' . $e->getMessage());
                    }
                }
                // re-collect after deletion
                $col = $col->slice(0, $keep);
            }

            $files = $col->map(function ($f) {
                return [
                    'filename' => $f['filename'],
                    'size' => $f['size'],
                    'modified' => $f['modified'],
                    'url' => $f['url'],
                ];
            })->all();
        }

        return view('admin.stagiaires.reports', compact('files'));
    }

    /**
     * Purge reports on demand (admin action)
     */
    public function purgeReports(Request $request)
    {
        $reportsDir = storage_path('reports');
        $deleted = 0;
        if (File::exists($reportsDir)) {
            $files = collect(File::files($reportsDir))->filter(function ($f) {
                return preg_match('/^import_stagiaires_\d{8}_\d{6}\.txt$/', $f->getFilename());
            });

            foreach ($files as $f) {
                try {
                    File::delete($f->getPathname());
                    $deleted++;
                } catch (\Exception $e) {
                    Log::warning('Erreur lors de la purge du rapport ' . $f->getFilename() . ': ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('stagiaires.import.reports')->with('success', "$deleted rapports supprimés.");
    }

    /**
     * Endpoint pour vérifier l'état de l'import (AJAX polling)
     */
    public function importStatus()
    {
        $lastJob = ImportJob::orderBy('created_at', 'desc')->first();
        $running = $lastJob ? in_array($lastJob->status, ['queued', 'running']) : false;
        $lastReport = $lastJob ? $lastJob->report_filename : null;

        return response()->json([
            'running' => (bool) $running,
            'lastReport' => $lastReport,
        ]);
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
