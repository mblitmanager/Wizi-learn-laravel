<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStagiaireRequest;
use App\Models\CatalogueFormation;
use App\Models\Commercial;
use App\Models\Formateur;
use App\Models\Formation;
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
        return view('admin.stagiaires.create', compact('formations', 'formateurs', 'commercials'));
    }

    public function store(StoreStagiaireRequest $request): RedirectResponse
    {
        $this->stagiaireService->create($request->validated());

        return redirect()->route('stagiaires.index')
            ->with('success', 'Le stagiaire a été créé avec succès.');
    }

    public function edit($id): View
    {
        $stagiaire = $this->stagiaireService->show($id);

        $formations = CatalogueFormation::all();
        $formateurs = Formateur::all();
        $commercials = Commercial::all();
        return view('admin.stagiaires.edit', compact('formations', 'formateurs', 'commercials', 'stagiaire'));
    }

    public function update(StoreStagiaireRequest $request, $id): RedirectResponse
    {
        $this->stagiaireService->update($id, $request->validated());

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
            // fallback : parser texte si c’est une vraie date genre "12/01/1990"
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
                ['Date de naissance', 'Datedenaissance', 'Date Naissance']
            ];
            $headerValues = [];
            $headerCells->rewind();
            // On ne prend que les 8 premières colonnes (même si le fichier en a plus)
            for ($i = 0; $i < 8; $i++) {
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
            foreach ($expectedHeadersNormalized as $index => $possibleHeaders) {
                if (!isset($headerValues[$index]) || !in_array($headerValues[$index], $possibleHeaders)) {
                    $officialHeader = is_array($expectedHeaders[$index]) ? $expectedHeaders[$index][0] : $expectedHeaders[$index];
                    $headerErrors[] = "Colonne " . ($index + 1) . ": Attendu '{$officialHeader}'";
                }
            }

            return redirect()->route('stagiaires.index')
                ->with('error', new \Illuminate\Support\HtmlString(
                    'En-têtes incorrects:<br>' . implode('<br>', $headerErrors) .
                        '<br>Veuillez utiliser le modèle fourni.'
                ));



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
                    if (count($data) === 8) {
                        break;
                    }
                }

                // Si moins de 8 valeurs, on complète avec des chaînes vides
                while (count($data) < 8) {
                    $data[] = '';
                }

                // // Vérifier que la ligne contient exactement 8 colonnes comme dans l'en-tête
                if (count($data) !== 8) {
                    $invalidRows[] = 'Ligne ' . $row->getRowIndex() . ': Nombre de colonnes incorrect';
                    continue;
                }

                list($civilite, $tiers, $email, $telephone, $ville, $codePostal, $adresse, $dateNaissance) = $data;
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

                    Stagiaire::create([
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
                $message .= "<br>" . count($ignoredEmails) . " doublons ignorés";
            }

            if (count($invalidRows) > 0) {
                $message .= "<br>" . count($invalidRows) . ' ' . (count($invalidRows) === 1 ? 'erreur' : 'erreurs');
            }


            // Retour avec le statut approprié
            if ($importedCount > 0) {
                return redirect()->route('stagiaires.index')
                    ->with('success', $message);
            } else {
                return redirect()->route('stagiaires.index')
                    ->with('error', $message);
            }
        } catch (\Exception $e) {
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
