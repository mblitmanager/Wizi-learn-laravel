<?php

namespace App\Jobs;

use App\Models\CatalogueFormation;
use App\Models\Formation;
use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportStagiairesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $storedPath;
    protected $importJobId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $storedPath, ?int $importJobId = null)
    {
        // $storedPath is the relative path returned by store/storeAs (eg: "imports/filename.xlsx")
        $this->storedPath = $storedPath;
        $this->importJobId = $importJobId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // mark job as running in DB (if provided) and broadcast
        $importJob = null;
        if ($this->importJobId) {
            $importJob = \App\Models\ImportJob::find($this->importJobId);
            if ($importJob) {
                $importJob->update(['status' => 'running', 'started_at' => now()]);
            }
        }

        event(new \App\Events\ImportStatusUpdated('running', null));

        try {
            // Resolve the storage path via the Storage facade (disk 'local') to be robust across workers
            $fullPath = null;
            try {
                $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($this->storedPath ?? '');
            } catch (\Exception $ex) {
                // fallback
                $fullPath = storage_path('app/' . ($this->storedPath ?? ''));
            }

            if (!file_exists($fullPath)) {
                // better debug info: list files in imports dir
                $importsDir = storage_path('app/imports');
                $listing = [];
                try {
                    if (is_dir($importsDir)) {
                        $files = array_map('basename', array_values(array_filter(glob($importsDir . DIRECTORY_SEPARATOR . '*'))));
                        $listing = $files;
                    }
                } catch (\Exception $_e) {
                    // ignore
                }

                $msg = 'File "' . $fullPath . '" does not exist.' . (empty($listing) ? '' : ' Imports dir files: ' . implode(', ', $listing));
                Log::error('Erreur ImportStagiairesJob: ' . $msg);
                if ($importJob) {
                    $importJob->update(['status' => 'failed', 'details' => $msg, 'finished_at' => now()]);
                }
                event(new \App\Events\ImportStatusUpdated('failed', null));
                return;
            }

            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();

            $ignoredEmails = [];
            $invalidRows = [];
            $importedCount = 0;
            $updatedCount = 0;
            $updatedDetails = [];

            // header validation similar to controller (assume same template)
            $headerRow = $sheet->getRowIterator()->current();
            $headerCells = $headerRow->getCellIterator();
            $expectedHeaders = [
                'Civilité', 'Tiers', 'Email', 'Téléphone', 'Ville', ['Code postal', 'Codepostal', 'Code Postal'], 'Adresse', ['Date de naissance', 'Datedenaissance', 'Date Naissance'], 'Formation', 'Date de début de formation', 'Date de fin de formation', 'Date d\'inscriptions', 'mot de passe',
            ];
            $headerValues = [];
            $headerCells->rewind();
            for ($i = 0; $i < 13; $i++) {
                if ($headerCells->valid()) {
                    $headerValues[] = preg_replace('/\s+/', '', strtolower(trim($headerCells->current()->getValue())));
                    $headerCells->next();
                } else {
                    $headerValues[] = '';
                }
            }

            $expectedHeadersNormalized = [];
            foreach ($expectedHeaders as $header) {
                if (is_array($header)) {
                    $normalized = array_map(function ($h) {
                        return preg_replace('/\s+/', '', strtolower($h));
                    }, $header);
                    $expectedHeadersNormalized[] = $normalized;
                } else {
                    $expectedHeadersNormalized[] = [preg_replace('/\s+/', '', strtolower($header))];
                }
            }

            $headerIsValid = true;
            foreach ($expectedHeadersNormalized as $index => $possibleHeaders) {
                if (!isset($headerValues[$index]) || !in_array($headerValues[$index], $possibleHeaders)) {
                    $headerIsValid = false;
                    break;
                }
            }

            if (!$headerIsValid) {
                Log::error('Import stagiaires: en-têtes incorrects dans le fichier importé.');
                return;
            }

            foreach ($sheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];
                foreach ($cellIterator as $cell) {
                    $value = trim($cell->getValue());
                    $data[] = $value;
                    if (count($data) === 13) {
                        break;
                    }
                }
                while (count($data) < 13) {
                    $data[] = '';
                }

                $rowIndex = $row->getRowIndex();
                if (count($data) !== 13) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Nombre de colonnes incorrect'];
                    continue;
                }

                list($civilite, $tiers, $email, $telephone, $ville, $codePostal, $adresse, $dateNaissance, $formation, $dateDebutFormation, $dateFinFormation, $dateInscription, $password) = $data;

                if (empty($email) || empty($tiers)) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Champs obligatoires manquants (tiers ou email)'];
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Email invalide'];
                    continue;
                }

                // manage existing user
                $existingUser = User::where('email', $email)->first();
                if ($existingUser) {
                    $existingStagiaire = Stagiaire::where('user_id', $existingUser->id)->first();
                    if (!$existingStagiaire) {
                        $existingStagiaire = Stagiaire::create([
                            'civilite' => $civilite ?: null,
                            'prenom' => null,
                            'nom' => null,
                            'telephone' => $telephone ?: null,
                            'adresse' => $adresse ?: null,
                            'date_naissance' => null,
                            'ville' => $ville ?: null,
                            'code_postal' => $codePostal ?: null,
                            'user_id' => $existingUser->id,
                            'role' => 'stagiaire',
                            'statut' => true,
                        ]);
                    }

                    if (!empty(trim($formation))) {
                        $formationTitre = trim($formation);
                        $normalized = $this->normalizeString($formationTitre);
                        $catalogueFormation = CatalogueFormation::whereRaw("REPLACE(LOWER(titre),' ', '') = ?", [$normalized])->first();
                        if (!$catalogueFormation) {
                            $formationModel = Formation::whereRaw("REPLACE(LOWER(titre),' ', '') = ?", [$normalized])->first();
                            if ($formationModel) {
                                $catalogueFormation = CatalogueFormation::where('formation_id', $formationModel->id)->first();
                            }
                        }
                        if (!$catalogueFormation) {
                            $catalogueFormation = CatalogueFormation::whereRaw('LOWER(titre) LIKE ?', ['%'.mb_strtolower($formationTitre, 'UTF-8').'%'])->first();
                        }

                        if ($catalogueFormation) {
                            $existsPivot = DB::table('stagiaire_catalogue_formations')
                                ->where('stagiaire_id', $existingStagiaire->id)
                                ->where('catalogue_formation_id', $catalogueFormation->id)
                                ->exists();
                            if (!$existsPivot) {
                                DB::table('stagiaire_catalogue_formations')->insert([
                                    'stagiaire_id' => $existingStagiaire->id,
                                    'catalogue_formation_id' => $catalogueFormation->id,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $updatedCount++;
                                $updatedDetails[] = "Ligne $rowIndex : formation '$formationTitre' ajoutée à stagiaire existant (email: $email)";
                            }
                        } else {
                            $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => "Formation \"$formationTitre\" non trouvée (pour utilisateur existant)"];
                        }
                    }

                    continue;
                }

                // parse name
                $np = $this->extraireNomPrenom($tiers);
                if (empty($np['nom']) || empty($np['prenom'])) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Format du nom/prénom invalide'];
                    continue;
                }

                $dateNaissance = $this->convertExcelDate($dateNaissance);
                $dateDebutFormation = $this->convertExcelDate($dateDebutFormation);
                $dateFinFormation = $this->convertExcelDate($dateFinFormation);
                $dateInscription = $this->convertExcelDate($dateInscription);

                DB::beginTransaction();
                try {
                    $user = User::create([
                        'name' => $np['nom'],
                        'email' => $email,
                        'password' => bcrypt($password) ?? bcrypt('password'),
                        'role' => 'stagiaire',
                    ]);

                    $stagiaire = Stagiaire::create([
                        'civilite' => $civilite ?: null,
                        'prenom' => $np['prenom'],
                        'nom' => $np['nom'],
                        'telephone' => $telephone ?: null,
                        'adresse' => $adresse ?: null,
                        'date_naissance' => $dateNaissance ?: null,
                        'ville' => $ville ?: null,
                        'code_postal' => $codePostal ?: null,
                        'user_id' => $user->id,
                        'role' => 'stagiaire',
                        'statut' => true,
                        'date_debut_formation' => $dateDebutFormation ?: null,
                        'date_fin_formation' => $dateFinFormation ?: null,
                        'date_inscription' => $dateInscription ?: null,
                    ]);

                    if (!empty(trim($formation))) {
                        $formationTitre = trim($formation);
                        $normalized = $this->normalizeString($formationTitre);
                        $catalogueFormation = CatalogueFormation::whereRaw("REPLACE(LOWER(titre),' ', '') = ?", [$normalized])->first();
                        if (!$catalogueFormation) {
                            $formationModel = Formation::whereRaw("REPLACE(LOWER(titre),' ', '') = ?", [$normalized])->first();
                            if ($formationModel) {
                                $catalogueFormation = CatalogueFormation::where('formation_id', $formationModel->id)->first();
                            }
                        }
                        if (!$catalogueFormation) {
                            $catalogueFormation = CatalogueFormation::whereRaw('LOWER(titre) LIKE ?', ['%'.mb_strtolower($formationTitre, 'UTF-8').'%'])->first();
                        }

                        if ($catalogueFormation) {
                            DB::table('stagiaire_catalogue_formations')->insert([
                                'stagiaire_id' => $stagiaire->id,
                                'catalogue_formation_id' => $catalogueFormation->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => "Formation \"$formationTitre\" non trouvée (vérifier le titre dans CatalogueFormation ou Formation)"];
                            DB::rollBack();
                            continue;
                        }
                    }

                    DB::commit();
                    $importedCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Erreur lors de la création : ' . $e->getMessage()];
                }
            }

            // generate report
            $reportLines = [];
            $reportLines[] = 'Rapport d\'import des stagiaires - ' . now()->format('Y-m-d H:i');
            $reportLines[] = '';
            $reportLines[] = "Importés : $importedCount";
            $reportLines[] = "Mises à jour (formations ajoutées) : $updatedCount";
            $reportLines[] = '';
            if (!empty($updatedDetails)) {
                $reportLines[] = 'Détails des mises à jour:';
                foreach ($updatedDetails as $d) {
                    $reportLines[] = $d;
                }
                $reportLines[] = '';
            }
            if (!empty($ignoredEmails)) {
                $reportLines[] = 'Emails ignorés (doublons non traités) :';
                $reportLines[] = implode(', ', $ignoredEmails);
                $reportLines[] = '';
            }
            if (!empty($invalidRows)) {
                $reportLines[] = 'Erreurs détectées :';
                foreach ($invalidRows as $err) {
                    if (is_array($err)) {
                        $reportLines[] = 'Ligne ' . ($err['ligne'] ?? '?') . ' : ' . ($err['erreur'] ?? json_encode($err));
                    } else {
                        $reportLines[] = (string)$err;
                    }
                }
                $reportLines[] = '';
            }

            $reportsDir = storage_path('reports');
            if (!File::exists($reportsDir)) {
                File::makeDirectory($reportsDir, 0755, true);
            }
            $reportFilename = 'import_stagiaires_' . now()->format('Ymd_His') . '.txt';
            $reportPath = $reportsDir . DIRECTORY_SEPARATOR . $reportFilename;
            File::put($reportPath, implode(PHP_EOL, $reportLines));
            // update DB job and broadcast completion
            if ($importJob) {
                $importJob->update([
                    'status' => 'completed',
                    'report_filename' => $reportFilename,
                    'finished_at' => now(),
                ]);
            }
            // broadcast
            event(new \App\Events\ImportStatusUpdated('completed', $reportFilename));
            Log::info('Import stagiaires terminé. Rapport: ' . $reportFilename);
        } catch (\Exception $e) {
            if ($importJob) {
                $importJob->update(['status' => 'failed', 'details' => $e->getMessage(), 'finished_at' => now()]);
            }
            event(new \App\Events\ImportStatusUpdated('failed', null));
            Log::error('Erreur ImportStagiairesJob: ' . $e->getMessage());
        } finally {
            // final cleanup broadcast if still running
            try {
                event(new \App\Events\ImportStatusUpdated('idle', null));
            } catch (\Exception $e) {
                Log::warning('Erreur broadcast final import status: ' . $e->getMessage());
            }
        }
    }

    private function normalizeString($value)
    {
        if ($value === null) {
            return '';
        }
        $s = mb_strtolower(trim($value), 'UTF-8');
        $trans = @iconv('UTF-8', 'ASCII//TRANSLIT', $s);
        if ($trans !== false) {
            $s = $trans;
        }
        $s = preg_replace('/\s+/', '', $s);
        $s = preg_replace('/[^a-z0-9]/', '', $s);
        return $s;
    }

    // Reuse some helper methods for parsing/ date conversion
    private function extraireNomPrenom($tiers)
    {
        $parts = preg_split('/\s+/', trim($tiers));
        if (isset($parts[0]) && is_numeric($parts[0])) {
            array_shift($parts);
        }
        $nom = [];
        $prenom = [];
        $hasUppercaseNom = false;
        foreach ($parts as $part) {
            if (mb_strtoupper($part, 'UTF-8') === $part) {
                $nom[] = mb_strtoupper($part, 'UTF-8');
                $hasUppercaseNom = true;
            } else {
                $prenom[] = ucfirst(mb_strtolower($part, 'UTF-8'));
            }
        }
        if (!$hasUppercaseNom && count($parts) >= 2) {
            $last = array_pop($parts);
            $nom = [mb_strtoupper($last, 'UTF-8')];
            $prenom = array_map(function ($p) {
                return ucfirst(mb_strtolower($p, 'UTF-8'));
            }, $parts);
        }
        if (empty($nom) || empty($prenom)) {
            return ['nom' => null, 'prenom' => null];
        }
        return [
            'nom' => implode(' ', $nom),
            'prenom' => implode(' ', $prenom),
        ];
    }

    private function convertExcelDate($value)
    {
        if (is_numeric($value)) {
            return \Carbon\Carbon::createFromTimestamp(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value))->format('Y-m-d');
        } else {
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}
