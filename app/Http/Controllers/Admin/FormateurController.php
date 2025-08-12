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
use Illuminate\Support\Facades\Log;
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
                ->with('error', 'Erreur: ' . $e->getMessage());
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
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $formateur = $this->formateurService->show($id);

            // Suppression de l'image du formateur s'il existe
            if ($formateur->image) {
                $imagePath = public_path('images/formateurs/' . $formateur->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }

            // Suppression du formateur
            $this->formateurService->delete($id);

            return redirect()->route('formateur.index')->with('success', 'Formateur supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('formateur.index')->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
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

            $results = [
                'imported' => 0,
                'ignored' => [],
                'errors' => [],
                'warnings' => []
            ];

            // Vérification des en-têtes selon votre fichier Excel
            $expectedHeaders = [
                'A' => 'email',
                'B' => 'nom',
                'C' => 'prenom',
                'D' => 'tel',
                'E' => 'adresse',
                'F' => 'formation'
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
                $email = trim($sheet->getCell('A' . $rowIndex)->getValue());
                $nom = trim($sheet->getCell('B' . $rowIndex)->getValue());
                $prenom = trim($sheet->getCell('C' . $rowIndex)->getValue());
                $tel = trim($sheet->getCell('D' . $rowIndex)->getValue());
                $adresse = trim($sheet->getCell('E' . $rowIndex)->getValue());
                $formationsInput = trim($sheet->getCell('F' . $rowIndex)->getValue());

                // Vérification des champs obligatoires
                $requiredFields = [
                    'email' => $email,
                    'nom' => $nom,
                    'prenom' => $prenom
                ];

                $missingFields = [];
                foreach ($requiredFields as $field => $value) {
                    if (empty($value)) {
                        $missingFields[] = $field;
                    }
                }

                if (!empty($missingFields)) {
                    $results['errors'][] = "Ligne $rowIndex: Champs obligatoires manquants: " . implode(', ', $missingFields);
                    continue;
                }

                // Validation de l'email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $results['errors'][] = "Ligne $rowIndex: Email invalide: '$email'";
                    continue;
                }

                DB::beginTransaction();
                try {
                    // Vérification des doublons
                    $existingUser = User::where('email', $email)->where('role', 'Formateur')->first();
                    if ($existingUser) {
                        $results['ignored'][] = "Ligne $rowIndex: L'utilisateur $email existe déjà";
                        DB::rollBack();
                        continue;
                    }

                    // Création de l'utilisateur
                    $user = User::create([
                        'name' => "$prenom $nom",
                        'email' => $email,
                        'password' => bcrypt('formateur@123'),
                        'role' => 'Formateur',
                        'adresse' => $adresse
                    ]);

                    // Formatage du téléphone
                    $tel = $this->formatPhoneNumber($tel);

                    // Création du formateur
                    $formateur = Formateur::create([
                        'prenom' => $prenom,
                        'nom' => $nom,
                        'telephone' => $tel,
                        'user_id' => $user->id,
                        'role' => 'Formateur',
                        'statut' => true,
                    ]);

                    // Gestion des formations (sélection multiple)
                    if (!empty($formationsInput)) {
                        $formations = array_map('trim', explode(',', $formationsInput));

                        foreach ($formations as $formationName) {
                            // Nettoyage et recherche flexible du nom de formation
                            $cleanedFormationName = $this->cleanFormationName($formationName);
                            $formation = CatalogueFormation::where('titre', 'like', "%$cleanedFormationName%")->first();

                            if ($formation) {
                                // Vérification si la relation existe déjà
                                $existingRelation = DB::table('formateur_catalogue_formation')
                                    ->where('formateur_id', $formateur->id)
                                    ->where('catalogue_formation_id', $formation->id)
                                    ->first();

                                if (!$existingRelation) {
                                    DB::table('formateur_catalogue_formation')->insert([
                                        'formateur_id' => $formateur->id,
                                        'catalogue_formation_id' => $formation->id,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                }
                            } else {
                                $results['warnings'][] = "Ligne $rowIndex: Formation '$formationName' non trouvée";
                            }
                        }
                    }

                    DB::commit();
                    $results['imported']++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $results['errors'][] = "Ligne $rowIndex: Erreur - " . $e->getMessage();
                    Log::error("Erreur import ligne $rowIndex", [
                        'email' => $email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Construction du message de résultat
            $message = "<strong>Résultat de l'importation :</strong><br>";
            $message .= "- Formateurs importés : {$results['imported']}<br>";

            if (!empty($results['ignored'])) {
                $message .= "- Doublons ignorés : " . count($results['ignored']) . "<br>";
            }

            if (!empty($results['warnings'])) {
                $message .= "- Avertissements : " . count($results['warnings']) . "<br>";
            }

            if (!empty($results['errors'])) {
                $message .= "- Erreurs : " . count($results['errors']) . "<br>";
            }

            // Préparation des données pour la vue
            $redirect = redirect()->route('formateur.index')
                ->with('import_results', new HtmlString($message));

            if (!empty($results['errors'])) {
                $redirect->with('import_errors', $results['errors']);
            }

            if (!empty($results['warnings'])) {
                $redirect->with('import_warnings', $results['warnings']);
            }

            if (!empty($results['ignored'])) {
                $redirect->with('import_ignored', $results['ignored']);
            }

            return $redirect;
        } catch (\Exception $e) {
            Log::error("Erreur globale d'import", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('formateur.index')
                ->with('error', "Erreur lors de l'import: " . $e->getMessage());
        }
    }

    // Fonction pour nettoyer les noms de formation
    private function cleanFormationName($name)
    {
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name); // Supprime les espaces multiples
        $name = str_replace(['FORMATION', 'formation'], '', $name); // Enlève le mot "FORMATION"
        return trim($name);
    }

    // Fonction pour formater les numéros de téléphone
    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 9) {
            return '0' . $phone; // Ajoute le 0 manquant pour les numéros français
        }

        return $phone;
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
