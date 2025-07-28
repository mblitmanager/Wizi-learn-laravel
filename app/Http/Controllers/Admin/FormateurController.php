<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormateurStoreRequest;
use App\Models\CatalogueFormation;
use App\Models\Formateur;
use App\Models\Stagiaire;
use App\Models\User;
use App\Services\FormateurService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\HtmlString;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FormateurController extends Controller
{
    protected $formateurService;
    public function __construct(FormateurService $formateurService)
    {
        $this->formateurService = $formateurService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formateurs = $this->formateurService->list();
        return view('admin.formateur.index', compact('formateurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $catalogue_formations = CatalogueFormation::all();
        $stagiaires = Stagiaire::all();
        return view('admin.formateur.create', compact('catalogue_formations', 'stagiaires'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormateurStoreRequest $request)
    {
        try {
            // Récupère les données validées
            $validatedData = $request->validated();
            // Ajoute manuellement le fichier image s'il existe
            if ($request->hasFile('image')) {
                $validatedData['image'] = $request->file('image');
            }

            $this->formateurService->create($validatedData);

            return redirect()->route('formateur.index')
                ->with('success', 'Création réussie');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erreur: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $formateur = $this->formateurService->show($id);
        return view('admin.formateur.show', compact('formateur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $formateur = $this->formateurService->show($id);
        $catalogue_formations = CatalogueFormation::all();
        $stagiaires = Stagiaire::all();
        return view('admin.formateur.edit', compact('formateur', 'catalogue_formations', 'stagiaires'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FormateurStoreRequest $request, string $id)
    {
        try {
            $this->formateurService->update($id, $request->validated());
            return redirect()->route('formateur.index')
                ->with('success', 'Mise à jour réussie');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erreur: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function splitConsultants($cellValue)
    {
        // Supprime les espaces en trop, puis découpe sur "et" ou ","
        $cleaned = preg_replace('/\s+et\s+|\s*,\s*/', '|', $cellValue);
        $parts = array_map('trim', explode('|', $cleaned));

        return array_filter($parts); // filtre les vides
    }

    private function extraireNomPrenom($fullName)
    {
        $fullName = trim(preg_replace('/\s+/', ' ', $fullName)); // Normaliser les espaces

        // Supprimer les numéros en début de ligne si existent
        $fullName = preg_replace('/^\d+\s*/', '', $fullName);

        $parts = explode(' ', $fullName);

        // Cas simple: 2 parties = prénom + nom
        if (count($parts) === 2) {
            return [
                'prenom' => ucfirst($parts[0]),
                'nom' => ucfirst($parts[1])
            ];
        }

        // Cas complexe: on prend le dernier mot comme nom, le reste comme prénom
        $nom = array_pop($parts);
        $prenom = implode(' ', $parts);

        return [
            'prenom' => ucfirst($prenom),
            'nom' => ucfirst($nom)
        ];
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

            // Vérification des en-têtes
            $headerRow = $sheet->getRowIterator()->current();
            $headerCells = $headerRow->getCellIterator();
            $headerCells->setIterateOnlyExistingCells(false);

            // Vérification des 2 premières colonnes
            $expectedHeaders = [
                'A' => 'Consultant Formateur',
                'B' => 'Formation'
            ];

            $headerErrors = [];
            foreach ($expectedHeaders as $column => $expectedHeader) {
                $cellValue = trim($sheet->getCell($column . '1')->getValue() ?? '');
                if (mb_strtolower($cellValue) !== mb_strtolower($expectedHeader)) {
                    $headerErrors[] = "Colonne $column: En-tête attendu '$expectedHeader' mais trouvé '$cellValue'";
                }
            }

            if (!empty($headerErrors)) {
                return redirect()->route('formateur.index')
                    ->with('error', new HtmlString(
                        'En-têtes incorrects:<br>' . implode('<br>', $headerErrors) .
                            '<br>Veuillez utiliser le modèle fourni.'
                    ));
            }

            $lastRow = $sheet->getHighestDataRow();

            for ($rowIndex = 2; $rowIndex <= $lastRow; $rowIndex++) {
                $consultantCell = trim($sheet->getCell('A' . $rowIndex)->getValue());
                $formationCell = trim($sheet->getCell('B' . $rowIndex)->getValue());

                // Si la cellule consultant est vide, on passe à la ligne suivante
                if (empty($consultantCell)) {
                    \Log::info("Ligne $rowIndex vide - Ignorée.");
                    continue;
                }

                \Log::info("Ligne $rowIndex : Consultant: $consultantCell | Formation: $formationCell");

                $consultants = $this->splitConsultants($consultantCell);

                foreach ($consultants as $consultant) {
                    DB::beginTransaction();
                    try {
                        $np = $this->extraireNomPrenom($consultant);

                        if (empty($np['nom']) || empty($np['prenom'])) {
                            $invalidRows[] = 'Ligne ' . $rowIndex . ': Format du nom/prénom invalide - "' . $consultant . '"';
                            DB::rollBack();
                            continue;
                        }

                        $email = trim(strtolower(preg_replace('/[^a-z]/', '', $np['prenom']))) . '.' .
                            trim(strtolower(preg_replace('/[^a-z]/', '', $np['nom']))) . '@example.com';

                        if ($email === '@example.com') {
                            $invalidRows[] = 'Ligne ' . $rowIndex . ': Email invalide - "' . $consultant . '"';
                            DB::rollBack();
                            continue;
                        }

                        \Log::info("Import - Ligne {$rowIndex}", [
                            'consultant' => $consultant,
                            'np' => $np,
                            'email' => $email
                        ]);

                        $existingUser = User::where('email', $email)
                            ->where('role', 'Formateur')
                            ->first();

                        if ($existingUser) {
                            $ignoredEmails[] = $email;
                            DB::rollBack();
                            continue;
                        }

                        $user = User::create([
                            'name' => $np['prenom'] . ' ' . $np['nom'],
                            'email' => $email,
                            'password' => bcrypt('pole123'),
                            'role' => 'pole relation client',
                        ]);

                        $formateur  = Formateur::create([
                            'prenom' => $np['prenom'],
                            'nom' => $np['nom'],
                            'user_id' => $user->id,
                            'role' => 'Formateur',
                            'statut' => true,
                        ]);

                        if (!empty($formationCell)) {
                            $formation = CatalogueFormation::where('titre', $formationCell)->first();

                            if ($formation) {
                                DB::table('formateur_catalogue_formation')->insert([
                                    'formateur_id' => $formateur->id,
                                    'catalogue_formation_id' => $formation->id ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            } else {
                                \Log::warning("Formation non trouvée: $formationCell");
                                // Vous pouvez choisir d'ajouter une erreur ou juste logger
                                $invalidRows[] = 'Ligne ' . $rowIndex . ': Formation non trouvée - "' . $formationCell . '"';
                            }
                        }

                        DB::commit();
                        $importedCount++;
                    } catch (\Exception $e) {
                        \Log::error("Erreur d'import - Ligne {$rowIndex}", [
                            'consultant' => $consultant,
                            'np' => $np,
                            'error' => $e->getMessage()
                        ]);
                        DB::rollBack();
                        $invalidRows[] = 'Ligne ' . $rowIndex . ': Erreur - ' . $e->getMessage();
                    }
                }
            }



            // Construction du message de résultat
            $message = "Importation terminée";
            if ($importedCount > 0) {
                $message .= ": $importedCount Formateurs importés";
            }
            if (count($ignoredEmails) > 0) {
                $message .= "<br>" . count($ignoredEmails) . " doublons ignorés : " . implode(', ', $ignoredEmails);
            }

            if (count($invalidRows) > 0) {
                $message .= "<br>" . count($invalidRows) . ' ' . (count($invalidRows) === 1 ? 'erreur' : 'erreurs');
            }

            // Retour avec le statut approprié
            $redirect = redirect()->route('formateur.index');
            if (count($ignoredEmails) > 0) {
                $message = "<br>" . count($ignoredEmails) . " doublons ignorés";
                return $redirect->with([
                    'ignoredEmails' => $ignoredEmails,
                    'ignoredMessage' => new HtmlString($message)
                ]);
            }

            if ($importedCount > 0) {
                return $redirect->with('success', new HtmlString($message));
            } elseif (count($invalidRows) > 0) {
                return $redirect->with('error', new HtmlString($message));
            } else {
                return $redirect->with('info', 'Aucun Formateur importé (fichier vide ou seulement des doublons)');
            }
        } catch (\Exception $e) {
            return redirect()->route('formateur.index')
                ->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    public function downloadFormateurModel()
    {
        $filePath = public_path('models/formateur/formateur.xlsx');

        if (!File::exists($filePath)) {
            return redirect()->back()->with('error', 'Le fichier modèle est introuvable.');
        }

        $fileName = 'modele_import_formateur.xlsx';

        return Response::download($filePath, $fileName);
    }
}
