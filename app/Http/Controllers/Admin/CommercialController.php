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
        $this->commercialsService->create($request->validated());

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
    public function update(CommmercialStoreRequest $request, string $id)
    {
        $this->commercialsService->update($id, $request->validated());

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

            $ignoredEmails = [];
            $invalidRows = [];
            $importedCount = 0;

            // Vérification de l'en-tête
            $headerRow = $sheet->getRowIterator()->current();
            $headerCell = $headerRow->getCellIterator()->current();
            $headerValue = trim($headerCell->getValue());

            if (strtolower($headerValue) !== 'interlocuteur') {
                return redirect()->route('commercials.index')
                    ->with('error', 'En-tête incorrect. La première colonne doit être "Interlocuteur".');
            }

            $lastRow = $sheet->getHighestDataRow(); // Dernière ligne contenant des données

            for ($rowIndex = 2; $rowIndex <= $lastRow; $rowIndex++) {
                $cell = $sheet->getCell('A' . $rowIndex);
                $consultantsCell = trim($cell->getValue());

                // Si la cellule est vide, on la log et on passe à la ligne suivante
                if (empty($consultantsCell)) {
                    \Log::info("Ligne $rowIndex vide - Ignorée.");
                    continue;
                }

                \Log::info("Ligne $rowIndex : $consultantsCell");

                $consultants = $this->splitConsultants($consultantsCell);

                foreach ($consultants as $consultant) {
                    DB::beginTransaction();
                    try {
                        $np = $this->extraireNomPrenom($consultant);

                        if (empty($np['nom']) || empty($np['prenom'])) {
                            $invalidRows[] = 'Ligne ' . $rowIndex . ': Format du nom/prénom invalide - "' . $consultant . '"';
                            DB::rollBack();
                            continue;
                        }

                        // Génération d'email plus robuste
                        $email = strtolower(preg_replace('/[^a-z]/', '', $np['prenom'])) . '.' .
                            strtolower(preg_replace('/[^a-z]/', '', $np['nom'])) . '@example.com';

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

                        if (User::where('email', $email)->exists()) {
                            $ignoredEmails[] = $email;
                            DB::rollBack();
                            continue;
                        }

                        $user = User::create([
                            'name' => $np['prenom'] . ' ' . $np['nom'],
                            'email' => $email,
                            'password' => bcrypt('consultant123'),
                            'role' => 'commercial',
                        ]);

                        Commercial::create([
                            'prenom' => $np['prenom'],
                            'nom' => $np['nom'],
                            'user_id' => $user->id,
                            'role' => 'commercial',
                            'statut' => true,
                        ]);

                        DB::commit();
                        $importedCount++;
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $invalidRows[] = 'Ligne ' . $rowIndex . ': Erreur - ' . $e->getMessage();
                    }
                }
            }


            // Construction du message de résultat
            $message = "Importation terminée";
            if ($importedCount > 0) {
                $message .= ": $importedCount commerciaux importés";
            }
            if (count($ignoredEmails) > 0) {
                $message .= "<br>" . count($ignoredEmails) . " doublons ignorés : " . implode(', ', $ignoredEmails);
            }

            if (count($invalidRows) > 0) {
                $message .= "<br>" . count($invalidRows) . ' ' . (count($invalidRows) === 1 ? 'erreur' : 'erreurs');
            }

            // Retour avec le statut approprié
            $redirect = redirect()->route('commercials.index');
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
                return $redirect->with('info', 'Aucun commercial importé (fichier vide ou seulement des doublons)');
            }
        } catch (\Exception $e) {
            return redirect()->route('commercials.index')
                ->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
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
