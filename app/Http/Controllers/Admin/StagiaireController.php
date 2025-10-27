<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStagiaireRequest;
use App\Models\CatalogueFormation;
use App\Models\Formation;
use App\Models\Stagiaire;
use App\Models\User;
use App\Models\Formateur;
use App\Models\Commercial;
use App\Models\PoleRelationClient;
use App\Models\Partenaire;
use App\Jobs\ImportStagiairesJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Response;
use App\Models\ImportJob;
use Illuminate\Support\Facades\Hash;

class StagiaireController extends Controller
{
    public function import(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        // allow optional background flag (checkbox). If not present, default to true.
        $background = $request->boolean('background', true);

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

            // Pass the relative stored path to the job (safer when workers run on other processes)
            $relativePath = $storedPath; // 'imports/filename.xlsx'

            if ($background) {
                // Dispatch the job to run in background
                ImportStagiairesJob::dispatch($relativePath, $importJob->id);

                return redirect()->route('stagiaires.index')
                    ->with('success', 'Importation lancée en tâche de fond. Le rapport sera généré dans storage/reports une fois terminée.');
            } else {
                // Run synchronously (blocking) in this request by executing the job handler directly.
                try {
                    $job = new ImportStagiairesJob($relativePath, $importJob->id);
                    // call handle() directly to execute import inline
                    $job->handle();

                    // reload importJob to get updated report filename/status
                    $importJob->refresh();
                    $msg = 'Importation terminée.';
                    if (!empty($importJob->report_filename)) {
                        $msg .= ' Rapport: ' . $importJob->report_filename;
                    }

                    return redirect()->route('stagiaires.index')
                        ->with('success', $msg);
                } catch (\Exception $e) {
                    Log::error('Erreur import synchrone : ' . $e->getMessage());
                    return redirect()->route('stagiaires.index')
                        ->with('error', 'Erreur lors de l\'import synchrone: ' . $e->getMessage());
                }
            }
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
     * Affiche les détails d'un stagiaire
     */
    public function show($id)
    {
        // Load related data useful for admin view and stats
        $stagiaire = Stagiaire::with([
            'user',
            'catalogue_formations',
            'formateurs.user',
            'classements',
            'quizParticipations.quiz',
            'progressions',
            'watchedVideos'
        ])->findOrFail($id);

        // Calculate simple statistics
        $totalPoints = $stagiaire->classements->sum('points');

        $quizParticipations = $stagiaire->quizParticipations;
        $quizCompleted = $quizParticipations->where('status', 'completed');

        $totalScorePossible = $quizCompleted->sum(function ($p) {
            return $p->quiz->nb_points_total ?? 100;
        });
        $totalScoreObtained = $quizCompleted->sum('score');

        $progressionMoyenne = 0;
        if ($totalScorePossible > 0) {
            $progressionMoyenne = min(100, ($totalScoreObtained / $totalScorePossible) * 100);
        }

        $tempsProgressions = $stagiaire->progressions->sum('time_spent') ?? 0;
        $tempsQuiz = $quizParticipations->sum('time_spent') ?? 0;

        $statistiques = [
            'total_points' => $totalPoints,
            'quiz_completes' => $quizCompleted->count(),
            'quiz_total_participations' => $quizParticipations->count(),
            'progression_moyenne' => round($progressionMoyenne, 2),
            'temps_total_passe' => $tempsProgressions + $tempsQuiz,
            'derniere_activite' => $stagiaire->derniere_activite ?? null,
        ];

        return view('admin.stagiaires.show', compact('stagiaire', 'statistiques'));
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

    /**
     * Show the form for editing the specified stagiaire.
     */
    public function edit($id)
    {
        $stagiaire = Stagiaire::with(['user', 'catalogue_formations', 'commercials', 'poleRelationClient'])->findOrFail($id);

        // Collections used by the edit form
        $formations = CatalogueFormation::with('formation')->orderBy('titre')->get();
        $formateurs = Formateur::with('user')->orderBy('id')->get();
        $commercials = Commercial::with('user')->orderBy('id')->get();
        $poleRelations = PoleRelationClient::with('user')->orderBy('id')->get();
        $partenaires = Partenaire::orderBy('identifiant')->get();

        return view('admin.stagiaires.edit', compact('stagiaire', 'formations', 'formateurs', 'commercials', 'poleRelations', 'partenaires'));
    }

    /**
     * Show form to create a new stagiaire
     */
    public function create()
    {
        $stagiaire = new Stagiaire();
        $formations = CatalogueFormation::with('formation')->orderBy('titre')->get();
        $formateurs = Formateur::with('user')->orderBy('id')->get();
        $commercials = Commercial::with('user')->orderBy('id')->get();
        $poleRelations = PoleRelationClient::with('user')->orderBy('id')->get();
        $partenaires = Partenaire::orderBy('identifiant')->get();

        return view('admin.stagiaires.create', compact('stagiaire', 'formations', 'formateurs', 'commercials', 'poleRelations', 'partenaires'));
    }

    /**
     * Store a newly created stagiaire and user
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'civilite' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'date_debut_formation' => 'nullable|date',
            'date_inscription' => 'nullable|date',
            'partenaire_id' => 'nullable|exists:partenaires,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'stagiaire',
            ]);

            $stagiaire = Stagiaire::create([
                'user_id' => $user->id,
                'civilite' => $data['civilite'] ?? null,
                'prenom' => $data['prenom'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'adresse' => $data['adresse'] ?? null,
                'ville' => $data['ville'] ?? null,
                'code_postal' => $data['code_postal'] ?? null,
                'date_naissance' => $data['date_naissance'] ?? null,
                'date_debut_formation' => $data['date_debut_formation'] ?? null,
                'date_inscription' => $data['date_inscription'] ?? null,
                'partenaire_id' => $data['partenaire_id'] ?? null,
                'statut' => 1,
            ]);

            // formations
            $formationsInput = $request->input('formations', []);
            if (!empty($formationsInput) && is_array($formationsInput)) {
                $sync = [];
                foreach ($formationsInput as $fid => $vals) {
                    if (isset($vals['selected']) && $vals['selected']) {
                        $sync[$fid] = [
                            'date_debut' => $vals['date_debut'] ?? null,
                            'date_inscription' => $vals['date_inscription'] ?? null,
                            'date_fin' => $vals['date_fin'] ?? null,
                            'formateur_id' => $vals['formateur_id'] ?? null,
                        ];
                    }
                }
                $stagiaire->catalogue_formations()->sync($sync);
            }

            // commercials
            if ($request->has('commercial_id')) {
                $commercialIds = array_filter((array)$request->input('commercial_id', []));
                $stagiaire->commercials()->sync($commercialIds);
            }

            if ($request->has('pole_relation_client_id')) {
                $poleIds = array_filter((array)$request->input('pole_relation_client_id', []));
                $stagiaire->poleRelationClient()->sync($poleIds);
            }

            DB::commit();

            return redirect()->route('stagiaires.show', $stagiaire->id)->with('success', 'Stagiaire créé.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création stagiaire: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified stagiaire in storage.
     */
    public function update(Request $request, $id)
    {
        $stagiaire = Stagiaire::with('user')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'civilite' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'partenaire_id' => 'nullable|exists:partenaires,id',
        ]);

        DB::beginTransaction();
        try {
            // Update user
            $user = $stagiaire->user;
            if ($user) {
                $user->name = $data['name'];
                $user->email = $data['email'];
                if (!empty($data['password'])) {
                    $user->password = Hash::make($data['password']);
                }
                $user->save();
            }

            // Update stagiaire fields
            $stagiaire->civilite = $data['civilite'] ?? $stagiaire->civilite;
            $stagiaire->prenom = $data['prenom'] ?? $stagiaire->prenom;
            $stagiaire->telephone = $data['telephone'] ?? $stagiaire->telephone;
            $stagiaire->adresse = $data['adresse'] ?? $stagiaire->adresse;
            $stagiaire->ville = $data['ville'] ?? $stagiaire->ville;
            $stagiaire->code_postal = $data['code_postal'] ?? $stagiaire->code_postal;
            $stagiaire->date_naissance = $data['date_naissance'] ?? $stagiaire->date_naissance;
            $stagiaire->partenaire_id = $data['partenaire_id'] ?? $stagiaire->partenaire_id;
            $stagiaire->save();

            // Sync formations (pivot) with pivot data if provided
            $formationsInput = $request->input('formations', []);
            if (!empty($formationsInput) && is_array($formationsInput)) {
                $sync = [];
                foreach ($formationsInput as $fid => $vals) {
                    if (isset($vals['selected']) && $vals['selected']) {
                        $sync[$fid] = [
                            'date_debut' => $vals['date_debut'] ?? null,
                            'date_inscription' => $vals['date_inscription'] ?? null,
                            'date_fin' => $vals['date_fin'] ?? null,
                            'formateur_id' => $vals['formateur_id'] ?? null,
                        ];
                    }
                }
                // sync will replace existing relations; if empty, detach all
                $stagiaire->catalogue_formations()->sync($sync);
            }

            // Sync commercials and pole relation clients if present
            if ($request->has('commercial_id')) {
                $commercialIds = array_filter((array)$request->input('commercial_id', []));
                $stagiaire->commercials()->sync($commercialIds);
            }

            if ($request->has('pole_relation_client_id')) {
                $poleIds = array_filter((array)$request->input('pole_relation_client_id', []));
                $stagiaire->poleRelationClient()->sync($poleIds);
            }

            DB::commit();

            return redirect()->route('stagiaires.show', $stagiaire->id)->with('success', 'Stagiaire mis à jour.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour stagiaire: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
}
