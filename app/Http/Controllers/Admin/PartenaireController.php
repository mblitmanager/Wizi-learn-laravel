<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PartenaireController extends Controller
{
    public function show($id)
    {
        $partenaire = Partenaire::with('stagiaires.user')->findOrFail($id);
        return view('admin.partenaires.show', compact('partenaire'));
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /*******  9df1d27e-2ae9-45c1-b4b4-1b9537456af1  *******/
    public function index()
    {
        $partenaires = Partenaire::with('stagiaires')->get();
        return view('admin.partenaires.index', compact('partenaires'));
    }

    public function create()
    {
        $stagiaires = Stagiaire::all();
        return view('admin.partenaires.create', compact('stagiaires'));
    }

    public function store(Request $request)
    {
        // Pré-filtrer les contacts vides avant validation
        $rawContacts = $request->input('contacts', []);
        if (is_array($rawContacts)) {
            $filteredContacts = array_values(array_filter($rawContacts, function ($c) {
                return is_array($c) && (
                    !empty(trim($c['nom'] ?? '')) ||
                    !empty(trim($c['prenom'] ?? '')) ||
                    !empty(trim($c['fonction'] ?? '')) ||
                    !empty(trim($c['email'] ?? '')) ||
                    !empty(trim($c['tel'] ?? ''))
                );
            }));
            $request->merge(['contacts' => $filteredContacts]);
        }

        $data = $request->validate([
            'identifiant' => 'required|unique:partenaires',
            'adresse' => 'required',
            'ville' => 'required',
            'departement' => 'required',
            'code_postal' => 'required',
            'type' => 'required',
            'stagiaires' => 'array',
            'logo' => 'nullable|image|max:2048',
            'contacts' => 'nullable|array|max:3',
            'contacts.*.nom' => 'nullable|string|max:255',
            'contacts.*.prenom' => 'nullable|string|max:255',
            'contacts.*.fonction' => 'nullable|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.tel' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = uniqid('logo_') . '.' . $logoFile->getClientOriginalExtension();
            $destination = public_path('partenaires');
            if (!is_dir($destination)) {
                @mkdir($destination, 0775, true);
            }
            $logoFile->move($destination, $logoName);
            $data['logo'] = 'partenaires/' . $logoName;
        }

        // Les contacts sont déjà filtrés avant validation

        $partenaire = Partenaire::create($data);
        if (!empty($data['stagiaires'])) {
            $partenaire->stagiaires()->sync($data['stagiaires']);
        }
        return redirect()->route('partenaires.index')->with('success', 'Partenaire créé avec succès');
    }

    public function edit($id)
    {
        $partenaire = Partenaire::with('stagiaires')->findOrFail($id);
        $stagiaires = Stagiaire::all();
        return view('admin.partenaires.edit', compact('partenaire', 'stagiaires'));
    }

    public function update(Request $request, $id)
    {
        // Pré-filtrer les contacts vides avant validation
        $rawContacts = $request->input('contacts', []);
        if (is_array($rawContacts)) {
            $filteredContacts = array_values(array_filter($rawContacts, function ($c) {
                return is_array($c) && (
                    !empty(trim($c['nom'] ?? '')) ||
                    !empty(trim($c['prenom'] ?? '')) ||
                    !empty(trim($c['fonction'] ?? '')) ||
                    !empty(trim($c['email'] ?? '')) ||
                    !empty(trim($c['tel'] ?? ''))
                );
            }));
            $request->merge(['contacts' => $filteredContacts]);
        }

        $data = $request->validate([
            'identifiant' => 'required|unique:partenaires,identifiant,' . $id,
            'adresse' => 'required',
            'ville' => 'required',
            'departement' => 'required',
            'code_postal' => 'required',
            'type' => 'required',
            'stagiaires' => 'array',
            'logo' => 'nullable|image|max:2048',
            'contacts' => 'nullable|array|max:3',
            'contacts.*.nom' => 'nullable|string|max:255',
            'contacts.*.prenom' => 'nullable|string|max:255',
            'contacts.*.fonction' => 'nullable|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.tel' => 'nullable|string|max:50',
        ]);

        $partenaire = Partenaire::findOrFail($id);
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = uniqid('logo_') . '.' . $logoFile->getClientOriginalExtension();
            $destination = public_path('partenaires');
            if (!is_dir($destination)) {
                @mkdir($destination, 0775, true);
            }
            $logoFile->move($destination, $logoName);
            $data['logo'] = 'partenaires/' . $logoName;
        }

        // Les contacts sont déjà filtrés avant validation

        $partenaire->update($data);
        if (!empty($data['stagiaires'])) {
            $partenaire->stagiaires()->sync($data['stagiaires']);
        } else {
            $partenaire->stagiaires()->detach();
        }
        return redirect()->route('partenaires.index')->with('success', 'Partenaire mis à jour avec succès');
    }

    public function destroy($id)
    {
        $partenaire = Partenaire::findOrFail($id);
        $partenaire->stagiaires()->detach();
        $partenaire->delete();
        return redirect()->route('partenaires.index')->with('success', 'Partenaire supprimé avec succès');
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

            $invalidRows = [];
            $importedCount = 0;

            // Vérifier l'en-tête
            $headerRow = $sheet->getRowIterator()->current();
            $headerCells = $headerRow->getCellIterator();
            $expectedHeaders = [
                'Identifiant',
                'Adresse',
                'Ville',
                'Département',
                'Code postal',
                'Type',
                'Logo',
                'Contacts',
                'Actif'
            ];

            $headerValues = [];
            $headerCells->rewind();
            for ($i = 0; $i < 9; $i++) {
                if ($headerCells->valid()) {
                    $headerValues[] = preg_replace('/\s+/', '', strtolower(trim($headerCells->current()->getValue())));
                    $headerCells->next();
                } else {
                    $headerValues[] = '';
                }
            }

            $expectedHeadersNormalized = [];
            foreach ($expectedHeaders as $header) {
                $expectedHeadersNormalized[] = [preg_replace('/\s+/', '', strtolower($header))];
            }

            $headerErrors = [];
            $headerIsValid = true;
            foreach ($expectedHeadersNormalized as $index => $possibleHeaders) {
                if (!isset($headerValues[$index]) || !in_array($headerValues[$index], $possibleHeaders)) {
                    $officialHeader = $expectedHeaders[$index];
                    $headerErrors[] = 'Colonne ' . ($index + 1) . ": Attendu '{$officialHeader}'";
                    $headerIsValid = false;
                }
            }

            if (!$headerIsValid) {
                return redirect()->route('partenaires.index')
                    ->with('error', new HtmlString(
                        'En-têtes incorrects:<br>' . implode('<br>', $headerErrors) .
                            '<br>Veuillez utiliser le modèle fourni.'
                    ));
            }

            foreach ($sheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];

                foreach ($cellIterator as $cell) {
                    $value = trim($cell->getValue());
                    $data[] = $value;
                    if (count($data) === 9) {
                        break;
                    }
                }

                while (count($data) < 9) {
                    $data[] = '';
                }

                $rowIndex = $row->getRowIndex();

                if (count($data) !== 9) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Nombre de colonnes incorrect'];
                    continue;
                }

                list($identifiant, $adresse, $ville, $departement, $codePostal, $type, $logo, $contacts, $actif) = $data;

                // Validation des champs obligatoires
                if (empty($identifiant) || empty($type)) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Champs obligatoires manquants (identifiant ou type)'];
                    continue;
                }

                // Vérifier si l'identifiant existe déjà
                if (Partenaire::where('identifiant', $identifiant)->exists()) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Identifiant déjà existant'];
                    continue;
                }

                // Traitement du champ actif
                $actif = strtolower(trim($actif));
                $actif = in_array($actif, ['oui', 'yes', 'true', '1', 'vrai']) ? true : false;

                // Traitement des contacts (format JSON ou chaîne séparée)
                $contactsArray = [];
                if (!empty($contacts)) {
                    try {
                        // Essayer de parser comme JSON
                        $contactsArray = json_decode($contacts, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new \Exception('Format JSON invalide');
                        }

                        // Valider la structure des contacts
                        foreach ($contactsArray as $contact) {
                            if (!isset($contact['nom']) || !isset($contact['prenom']) || !isset($contact['email'])) {
                                throw new \Exception('Structure de contact invalide');
                            }
                        }
                    } catch (\Exception $e) {
                        $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Format de contacts invalide: ' . $e->getMessage()];
                        continue;
                    }
                }

                DB::beginTransaction();
                try {
                    $partenaire = Partenaire::create([
                        'identifiant' => $identifiant,
                        'adresse' => $adresse,
                        'ville' => $ville,
                        'departement' => $departement,
                        'code_postal' => $codePostal,
                        'type' => $type,
                        'logo' => $logo,
                        'contacts' => json_encode($contactsArray),
                        'actif' => $actif,
                    ]);

                    DB::commit();
                    $importedCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Erreur lors de la création : ' . $e->getMessage()];
                }
            }

            $message = 'Importation terminée.';
            if ($importedCount > 0) {
                $message .= " <strong>$importedCount</strong> partenaires importés.";
            }

            if (count($invalidRows) > 0) {
                Log::warning('Erreurs import partenaires : ' . json_encode($invalidRows));
                $message .= '<br><strong>' . count($invalidRows) . '</strong> erreurs détectées.';
                session()->flash('import_errors', $invalidRows);
            }

            return redirect()->route('partenaires.index')
                ->with($importedCount > 0 ? 'success' : 'error', new HtmlString($message));
        } catch (\Exception $e) {
            Log::error('Erreur import partenaires : ' . $e->getMessage());
            return redirect()->route('partenaires.index')
                ->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }
}
