<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommmercialStoreRequest;
use App\Models\Commercial;
use App\Models\Stagiaire;
use App\Models\User;
use App\Services\CommercialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\HtmlString;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CommercialController extends Controller
{
    protected $commercialsService;

    public function __construct(CommercialService $commercialsService)
    {
        $this->commercialsService = $commercialsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commercial = $this->commercialsService->list();
        return view('admin.commercial.index', compact('commercial'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stagiaires = Stagiaire::all();
        return view('admin.commercial.create', compact('stagiaires'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommmercialStoreRequest $request)
    {
        try {
            // Récupère les données validées
            $validatedData = $request->validated();
            // Ajoute manuellement le fichier image s'il existe
            if ($request->hasFile('image')) {
                $validatedData['image'] = $request->file('image');
            }

            $this->commercialsService->create($validatedData);

            return redirect()->route('commercials.index')
                ->with('success', 'Le commercial a été créé avec succès.');
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
        $commercial = $this->commercialsService->show($id);
        return view('admin.commercial.show', compact('commercial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $commercial = $this->commercialsService->show($id);
        $stagiaires = Stagiaire::all();
        return view('admin.commercial.edit', compact('commercial', 'stagiaires'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommmercialStoreRequest $request, string $id)
    {

        try {
            $this->commercialsService->update($id, $request->validated());
            return redirect()->route('commercials.index')
                ->with('success', 'Le commercial a été mis à jour avec succès.');
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
            $this->commercialsService->delete($id);
            return redirect()->route('commercials.index')
                ->with('success', 'Le commercial a été supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
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

            // CORRECTION : Ajout de la colonne civilité
            $expectedHeaders = [
                'A' => 'email',
                'B' => 'nom',
                'C' => 'prénom',
                'D' => 'civilite',
                'E' => 'tel',
                'F' => 'adresse'
            ];

            $headerErrors = [];
            foreach ($expectedHeaders as $column => $expectedHeader) {
                $cellValue = trim($sheet->getCell($column . '1')->getValue() ?? '');
                if (mb_strtolower($cellValue) !== mb_strtolower($expectedHeader)) {
                    $headerErrors[] = "Colonne $column: En-tête attendu '$expectedHeader' mais trouvé '$cellValue'";
                }
            }

            if (!empty($headerErrors)) {
                return redirect()->route('commercials.index')
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
                $civilite = trim($sheet->getCell('D' . $rowIndex)->getValue());
                $tel = trim($sheet->getCell('E' . $rowIndex)->getValue());
                $adresse = trim($sheet->getCell('F' . $rowIndex)->getValue());

                // Vérification des champs obligatoires
                $requiredFields = [
                    'email' => $email,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'civilite' => $civilite
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

                // Validation de la civilité
                $civilitesValides = ['M.', 'Mme.', 'Mlle.'];

                if (!in_array($civilite, $civilitesValides)) {
                    $results['errors'][] = "Ligne $rowIndex: Civilité invalide: '$civilite'. Valeurs acceptées: " . implode(', ', $civilitesValides);
                    continue;
                }

                DB::beginTransaction();
                try {
                    // CORRECTION : Déterminer le rôle en fonction de la civilité
                    $role = 'commercial'; // Par défaut
                    if ($civilite == 'Mme' || $civilite == 'Mlle') {
                        $role = 'commerciale';
                    } elseif ($civilite == 'M') {
                        $role = 'commercial';
                    }

                    // CORRECTION : Vérification des doublons avec les deux rôles
                    $existingUser = User::where('email', $email)->whereIn('role', ['commercial', 'commerciale'])->first();
                    if ($existingUser) {
                        $results['ignored'][] = "Ligne $rowIndex: L'utilisateur $email existe déjà";
                        DB::rollBack();
                        continue;
                    }

                    // Formatage du téléphone
                    $tel = $this->formatPhoneNumber($tel);

                    // Création de l'utilisateur
                    $user = User::create([
                        'name' => "$prenom $nom",
                        'email' => $email,
                        'password' => bcrypt('commercial@123'),
                        'role' => $role,
                        'adresse' => $adresse
                    ]);

                    // Création du commercial
                    Commercial::create([
                        'prenom' => $prenom,
                        'civilite' => $civilite,
                        'telephone' => $tel,
                        'user_id' => $user->id,
                        'role' => $role,
                        'statut' => true,
                    ]);

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
            $message .= "- Commerciaux importés : {$results['imported']}<br>";

            if (!empty($results['ignored'])) {
                $message .= "- Doublons ignorés : " . count($results['ignored']) . "<br>";
            }

            if (!empty($results['errors'])) {
                $message .= "- Erreurs : " . count($results['errors']) . "<br>";
            }

            // Préparation des données pour la vue
            $redirect = redirect()->route('commercials.index')
                ->with('import_results', new HtmlString($message));

            if (!empty($results['errors'])) {
                $redirect->with('import_errors', $results['errors']);
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
            return redirect()->route('commercials.index')
                ->with('error', "Erreur lors de l'import: " . $e->getMessage());
        }
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 9) {
            return '0' . $phone; // Ajoute le 0 manquant pour les numéros français
        }

        return $phone;
    }

    public function downloadCommercialModel()
    {
        $filePath = public_path('models/commercial/commercial.xlsx');

        if (!File::exists($filePath)) {
            return redirect()->back()->with('error', 'Le fichier modèle est introuvable.');
        }

        $fileName = 'modele_import_commercial.xlsx';

        return Response::download($filePath, $fileName);
    }
}
