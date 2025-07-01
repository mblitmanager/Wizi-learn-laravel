<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStagiaireRequest;
use App\Models\CatalogueFormation;
use App\Models\Commercial;
use App\Models\Formateur;
use App\Models\Stagiaire;
use App\Models\User;
use App\Services\StagiaireService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\HtmlString;
use App\Models\PoleRelationClient as PoleRelation;

class StagiaireController extends Controller
{
    protected $stagiaireService;

    public function __construct(StagiaireService $stagiaireService)
    {
        $this->stagiaireService = $stagiaireService;
    }

    public function index(): View
    {
        $stagiaires = $this->stagiaireService->list();
        return view('admin.stagiaires.index', compact('stagiaires'));
    }

    public function show($id): View
    {
        $stagiaire = $this->stagiaireService->show($id);
        return view('admin.stagiaires.show', compact('stagiaire'));
    }

    public function create(): View
    {
        $formations = CatalogueFormation::all();
        $formateurs = Formateur::all();
        $commercials = Commercial::all();
        $poleRelations = PoleRelation::all();
        return view('admin.stagiaires.create', compact('formations', 'formateurs', 'commercials', 'poleRelations'));
    }

    public function store(StoreStagiaireRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $formations = $request->input('formations', []);
        $selectedFormations = [];
        foreach ($formations as $formationId => $info) {
            if (isset($info['selected'])) {
                $selectedFormations[$formationId] = [
                    'date_debut' => $info['date_debut'] ?? null,
                    'date_inscription' => $info['date_inscription'] ?? null,
                    'date_fin' => $info['date_fin'] ?? null,
                    'formateur_id' => $info['formateur_id'] ?? null,
                ];
            }
        }
        $formateurIds = [];
        foreach ($selectedFormations as $formation) {
            if (!empty($formation['formateur_id'])) {
                $formateurIds[] = $formation['formateur_id'];
            }
        }
        $formateurIds = array_unique($formateurIds);
        $poleRelationClientIds = $request->input('pole_relation_client_id', []);
        $this->stagiaireService->create($data, $selectedFormations, $poleRelationClientIds, $formateurIds);
        return redirect()->route('stagiaires.index')
            ->with('success', 'Le stagiaire a été créé avec succès.');
    }

    public function edit($id): View
    {
        $stagiaire = $this->stagiaireService->show($id);

        $formations = CatalogueFormation::all();
        $formateurs = Formateur::all();
        $commercials = Commercial::all();
        $poleRelations = PoleRelation::all();
        return view('admin.stagiaires.edit', compact('formations', 'formateurs', 'commercials', 'stagiaire', 'poleRelations'));
    }

    public function update(StoreStagiaireRequest $request, $id): RedirectResponse
    {
        $data = $request->validated();
        $formations = $request->input('formations', []);
        $selectedFormations = [];
        foreach ($formations as $formationId => $info) {
            if (isset($info['selected'])) {
                $selectedFormations[$formationId] = [
                    'date_debut' => $info['date_debut'] ?? null,
                    'date_inscription' => $info['date_inscription'] ?? null,
                    'date_fin' => $info['date_fin'] ?? null,
                    'formateur_id' => $info['formateur_id'] ?? null,
                ];
            }
        }
        $formateurIds = [];
        foreach ($selectedFormations as $formation) {
            if (!empty($formation['formateur_id'])) {
                $formateurIds[] = $formation['formateur_id'];
            }
        }
        $formateurIds = array_unique($formateurIds);
        $poleRelationClientIds = $request->input('pole_relation_client_id', []);
        $this->stagiaireService->update($id, $data, $selectedFormations, $poleRelationClientIds, $formateurIds);
        return redirect()->route('stagiaires.index')
            ->with('success', 'Le stagiaire a été mis à jour avec succès.');
    }

    public function destroy($id): RedirectResponse
    {
        $this->stagiaireService->delete($id);

        return redirect()->route('stagiaires.index')
            ->with('success', 'Le stagiaire a été supprimé avec succès.');
    }

    public function desactive($id)
    {
        $this->stagiaireService->desactive($id);

        return redirect()->route('stagiaires.index')
            ->with('success', 'Le stagiaire a été désactivé avec succès.');
    }

    public function active($id)
    {
        $this->stagiaireService->active($id);

        return redirect()->route('stagiaires.index')
            ->with('success', 'Le stagiaire a été activé avec succès.');
    }

    function extraireNomPrenom($tiers)
    {
        // Supprimer les chiffres ou éléments inutiles en début de chaîne
        $parts = preg_split('/\s+/', trim($tiers));

        // Si le premier est un chiffre ou un mot tout en chiffres, on l'enlève
        if (is_numeric($parts[0])) {
            array_shift($parts);
        }

        $nom = [];
        $prenom = [];

        foreach ($parts as $part) {
            if (mb_strtoupper($part, 'UTF-8') === $part) {
                $nom[] = ucfirst(strtolower($part)); // pour normaliser
            } else {
                $prenom[] = ucfirst(strtolower($part));
            }
        }

        return [
            'nom' => implode(' ', $nom),
            'prenom' => implode(' ', $prenom),
        ];
    }

    private function convertExcelDate($value)
    {
        if (is_numeric($value)) {
            // Excel date -> PHP timestamp
            return \Carbon\Carbon::createFromTimestamp(
                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value)
            )->format('Y-m-d');
        } else {
            // fallback : parser texte si c'est une vraie date genre "12/01/1990"
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null; // ou un défaut si tu veux
            }
        }
    }


    public function import(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $ignoredEmails = [];
            $invalidRows = [];
            $importedCount = 0;

            // Vérifier l'en-tête en ignorant les espaces
            $headerRow = $sheet->getRowIterator()->current();
            $headerCells = $headerRow->getCellIterator();
            $expectedHeaders = [
                'Civilité',
                'Tiers',
                'Email',
                'Téléphone',
                'Ville',
                ['Code postal', 'Codepostal', 'Code Postal'],
                'Adresse',
                ['Date de naissance', 'Datedenaissance', 'Date Naissance'],
                'Formation'
            ];
            $headerValues = [];
            $headerCells->rewind();
            // On ne prend que les 8 premières colonnes (même si le fichier en a plus)
            for ($i = 0; $i < 9; $i++) {
                if ($headerCells->valid()) {
                    $headerValues[] = preg_replace('/\s+/', '', strtolower(trim($headerCells->current()->getValue())));
                    $headerCells->next();
                } else {
                    $headerValues[] = '';
                }
            }


            // Préparation des en-têtes attendus pour comparaison
            $expectedHeadersNormalized = [];
            foreach ($expectedHeaders as $header) {
                if (is_array($header)) {
                    // Si plusieurs variations sont possibles, on prend la première comme version "officielle"
                    $normalized = array_map(function ($h) {
                        return preg_replace('/\s+/', '', strtolower($h));
                    }, $header);
                    $expectedHeadersNormalized[] = $normalized;
                } else {
                    $expectedHeadersNormalized[] = [preg_replace('/\s+/', '', strtolower($header))];
                }
            }
            // Vérification des en-têtes
            $headerErrors = [];
            $headerIsValid = true;

            foreach ($expectedHeadersNormalized as $index => $possibleHeaders) {
                if (!isset($headerValues[$index]) || !in_array($headerValues[$index], $possibleHeaders)) {
                    $officialHeader = is_array($expectedHeaders[$index]) ? $expectedHeaders[$index][0] : $expectedHeaders[$index];
                    $headerErrors[] = "Colonne " . ($index + 1) . ": Attendu '{$officialHeader}'";
                    $headerIsValid = false;
                }
            }

            if (!$headerIsValid) {
                return redirect()->route('stagiaires.index')
                    ->with('error', new \Illuminate\Support\HtmlString(
                        'En-têtes incorrects:<br>' . implode('<br>', $headerErrors) .
                            '<br>Veuillez utiliser le modèle fourni.'
                    ));
            }



            foreach ($sheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];

                $data = [];
                foreach ($cellIterator as $cell) {
                    $value = trim($cell->getValue());

                    // On ignore les cellules vides
                    if ($value !== '') {
                        $data[] = $value;
                    }

                    // On arrête une fois qu'on a 8 valeurs non vides
                    if (count($data) === 9) {
                        break;
                    }
                }
                // Si moins de 8 valeurs, on complète avec des chaînes vides
                while (count($data) < 9) {
                    $data[] = '';
                }

                // // Vérifier que la ligne contient exactement 8 colonnes comme dans l'en-tête
                if (count($data) !== 9) {
                    $invalidRows[] = 'Ligne ' . $row->getRowIndex() . ': Nombre de colonnes incorrect';
                    continue;
                }

                list($civilite, $tiers, $email, $telephone, $ville, $codePostal, $adresse, $dateNaissance, $formation) = $data;
                // Validation des champs obligatoires
                if (empty($email) || empty($tiers) || empty($civilite)) {
                    $invalidRows[] = 'Ligne ' . $row->getRowIndex() . ': Champs obligatoires manquants';
                    continue;
                }

                // Validation de l'email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $invalidRows[] = 'Ligne ' . $row->getRowIndex() . ': Email invalide';
                    continue;
                }

                // Vérifie si l'utilisateur existe déjà par email
                if (User::where('email', $email)->exists()) {
                    $ignoredEmails[] = $email;
                    continue;
                }

                // Extraction du nom et prénom
                $np = $this->extraireNomPrenom($tiers);
                if (empty($np['nom']) || empty($np['prenom'])) {
                    $invalidRows[] = 'Ligne ' . $row->getRowIndex() . ': Format du nom/prénom invalide';
                    continue;
                }

                // Validation de la date de naissance
                try {
                    $dateNaissance = $this->convertExcelDate($dateNaissance);
                    if (!$dateNaissance) {
                        throw new \Exception('Date invalide');
                    }
                } catch (\Exception $e) {
                    $invalidRows[] = 'Ligne ' . $row->getRowIndex() . ': Date de naissance invalide';
                    continue;
                }

                // Tout est valide, on crée les enregistrements
                DB::beginTransaction();
                try {
                    $user = User::create([
                        'name' => $np['prenom'] . ' ' . $np['nom'],
                        'email' => $email,
                        'password' => bcrypt('stagiaire123'),
                        'role' => 'stagiaire',
                    ]);

                    $stagiaire = Stagiaire::create([
                        'civilite' => $civilite,
                        'prenom' => $np['prenom'],
                        'nom' => $np['nom'],
                        'telephone' => $telephone,
                        'adresse' => $adresse,
                        'date_naissance' => $dateNaissance,
                        'ville' => $ville,
                        'code_postal' => $codePostal,
                        'user_id' => $user->id,
                        'role' => 'stagiaire',
                        'statut' => true,
                        'date_debut_formation' => null,
                        'date_inscription' => now(),
                    ]);

                    // Si une formation est spécifiée, on l'associe
                    if (!empty(trim($data[8]))) {
                        // On supprime les espaces superflus et on cherche la formation
                        $formationTitre = trim($data[8]);
                        $formation = CatalogueFormation::where('titre', $formationTitre)->first();

                        DB::table('stagiaire_catalogue_formations')->insert([
                            'stagiaire_id' => $stagiaire->id,
                            'catalogue_formation_id' => $formation->id ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        if (!$formation) {
                            $invalidRows[] = 'Ligne ' . $row->getRowIndex() . ': Formation "' . $formationTitre . '" non trouvée';
                            DB::rollBack();
                            continue;
                        }
                    }
                    DB::commit();
                    $importedCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $invalidRows[] = 'Ligne ' . $row->getRowIndex() . ': Erreur lors de la création - ' . $e->getMessage();
                }
            }

            // dd($invalidRows);

            $message = "Importation terminée";
            if ($importedCount > 0) {
                $message .= ": $importedCount stagiaires importés";
            }

            if (count($ignoredEmails) > 0) {
                \Log::info('Emails ignorés : ' . implode(', ', $ignoredEmails));
                $message .= "<br>" . count($ignoredEmails) . " doublons ignorés : " . implode(', ', $ignoredEmails);
            }

            if (count($invalidRows) > 0) {

                $message .= "<br>" . count($invalidRows) . ' ' . (count($invalidRows) === 1 ? 'erreur' : 'erreurs');
            }


            // Retour avec le statut approprié
            if ($importedCount > 0) {
                return redirect()->route('stagiaires.index')
                    ->with('success', new HtmlString($message));
            } else {
                return redirect()->route('stagiaires.index')
                    ->with('error', new HtmlString($message));
            }
        } catch (\Exception $e) {
            \Log::error('Erreur import ligne ' . $row->getRowIndex() . ': ' . $e->getMessage());
            return redirect()->route('stagiaires.index')
                ->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
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
