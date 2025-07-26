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
        $data = $request->validated();

        // Gestion de l'image de profil
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = uniqid('commercial_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('media'), $filename);
            $data['image'] = 'media/' . $filename;
        }

        $this->commercialsService->create($data);

        return redirect()->route('commercials.index')
            ->with('success', 'Le commercial a été créé avec succès.');
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
    // public function update(CommmercialStoreRequest $request, string $id)
    // {

    //     $this->commercialsService->update($id, $request->validated());

    //     return redirect()->route('commercials.index')
    //         ->with('success', 'Le commercial a été mis à jour avec succès.');
    // }
    public function update(CommmercialStoreRequest $request, string $id)
    {
        $data = $request->validated();

        // Gestion de l'image de profil lors de la mise à jour
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = uniqid('commercial_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('media'), $filename);
            $data['image'] = 'media/' . $filename;
        }

        $this->commercialsService->update($id, $data);

        return redirect()->route('commercials.index')
            ->with('success', 'Le commercial a été mis à jour avec succès.');
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
                'C' => 'prénom',
                'D' => 'tel',
                'E' => 'adresse'
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

                    // Formatage du téléphone
                    $tel = $this->formatPhoneNumber($tel);

                    // Création de l'utilisateur
                    $user = User::create([
                        'name' => "$prenom $nom",
                        'email' => $email,
                        'password' => bcrypt('commercial@123'),
                        'role' => 'Commercial',
                        'adresse' => $adresse
                    ]);

                    // Création du commercial
                    Commercial::create([
                        'prenom' => $prenom,
                        'telephone' => $tel,
                        'user_id' => $user->id,
                        'role' => 'Commercial',
                        'statut' => true,
                    ]);

                    DB::commit();
                    $results['imported']++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $results['errors'][] = "Ligne $rowIndex: Erreur - " . $e->getMessage();
                    \Log::error("Erreur import ligne $rowIndex", [
                        'email' => $email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Construction du message de résultat
            $message = "<strong>Résultat de l'importation :</strong><br>";
            $message .= "- Commercial importés : {$results['imported']}<br>";

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
            \Log::error("Erreur globale d'import", [
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
