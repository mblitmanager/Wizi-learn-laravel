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
            'logo' => 'nullable|image|max:56048',
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
            'logo' => 'nullable|image|max:56048',
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
        ini_set('memory_limit', '256M'); // Augmenter la mémoire

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');

            // Utiliser un reader avec optimisation mémoire
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true); // IMPORTANT: Ne lire que les données
            $reader->setReadEmptyCells(false); // Ignorer les cellules vides

            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $invalidRows = [];
            $importedCount = 0;

            // OPTIMISATION: Lire l'en-tête directement sans itérateur
            $headerData = $sheet->rangeToArray('A1:M1')[0];
            $headerValues = array_map(function ($value) {
                return preg_replace('/\s+/', '', strtolower(trim($value ?? '')));
            }, $headerData);

            $expectedHeaders = [
                'Identifiant',
                'Adresse',
                'Ville',
                'Département',
                'Code postal',
                'Type',
                'Logo',
                'Nom du contact',
                'Prénom du contact',
                'Fonction du contact',
                'Mail',
                'Tél portable',
                'Statut'
            ];

            $expectedHeadersNormalized = array_map(function ($header) {
                return [preg_replace('/\s+/', '', strtolower($header))];
            }, $expectedHeaders);

            $headerErrors = [];
            $headerIsValid = true;
            foreach ($expectedHeadersNormalized as $index => $possibleHeaders) {
                if (!isset($headerValues[$index]) || !in_array($headerValues[$index], $possibleHeaders)) {
                    $headerErrors[] = 'Colonne ' . ($index + 1) . ": Attendu '{$expectedHeaders[$index]}'";
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

            // OPTIMISATION: Utiliser getHighestDataRow() au lieu de l'itérateur
            $highestRow = $sheet->getHighestDataRow();

            for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
                // OPTIMISATION: Lire toute la ligne en une fois
                $rowData = $sheet->rangeToArray('A' . $rowIndex . ':M' . $rowIndex)[0];
                $rowData = array_map('trim', $rowData);

                // Vérifier si la ligne est vide
                $isRowEmpty = true;
                foreach ($rowData as $value) {
                    if (!empty($value)) {
                        $isRowEmpty = false;
                        break;
                    }
                }

                if ($isRowEmpty) {
                    continue;
                }

                if (count($rowData) < 13) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Nombre de colonnes incorrect'];
                    continue;
                }

                list(
                    $identifiant,
                    $adresse,
                    $ville,
                    $departement,
                    $codePostal,
                    $type,
                    $logo,
                    $nomContact,
                    $prenomContact,
                    $fonctionContact,
                    $mail,
                    $telPortable,
                    $statut
                ) = $rowData;

                // Validation des champs obligatoires
                if (empty($identifiant)) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Champ obligatoire manquant (identifiant)'];
                    continue;
                }

                // Si le type est vide, mettre "CSE" par défaut
                if (empty($type)) {
                    $type = 'CSE';
                }

                // Vérifier si l'identifiant existe déjà
                if (Partenaire::where('identifiant', $identifiant)->exists()) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Identifiant déjà existant'];
                    continue;
                }

                // Traitement du champ actif
                $actif = strtolower(trim($statut));
                $actif = in_array($actif, ['signée', 'oui', 'yes', 'true', '1', 'vrai', 'actif']) ? true : false;

                // Traitement des contacts
                $contactsArray = $this->processContacts($nomContact, $prenomContact, $fonctionContact, $mail, $telPortable);

                try {
                    Partenaire::create([
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

                    $importedCount++;

                    // Libérer la mémoire périodiquement
                    if ($importedCount % 50 === 0) {
                        gc_collect_cycles();
                    }
                } catch (\Exception $e) {
                    $invalidRows[] = ['ligne' => $rowIndex, 'erreur' => 'Erreur lors de la création : ' . $e->getMessage()];
                }
            }

            // Libérer explicitement la mémoire
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $reader, $sheet);

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

    private function processContacts($nomContact, $prenomContact, $fonctionContact, $mail, $telPortable)
    {
        $contactsArray = [];

        $noms = preg_split('/\r\n|\n|\r|<br\s*\/?>/', $nomContact);
        $prenoms = preg_split('/\r\n|\n|\r|<br\s*\/?>/', $prenomContact);
        $fonctions = preg_split('/\r\n|\n|\r|<br\s*\/?>/', $fonctionContact);

        $mails = [];
        $mailLines = preg_split('/\r\n|\n|\r|<br\s*\/?>/', $mail);
        foreach ($mailLines as $mailLine) {
            $splitMails = explode(',', $mailLine);
            foreach ($splitMails as $splitMail) {
                $trimmedMail = trim($splitMail);
                if (!empty($trimmedMail)) {
                    $mails[] = $trimmedMail;
                }
            }
        }

        $telephones = preg_split('/\r\n|\n|\r|<br\s*\/?>/', $telPortable);

        $noms = array_map('trim', $noms);
        $prenoms = array_map('trim', $prenoms);
        $fonctions = array_map('trim', $fonctions);
        $mails = array_map('trim', $mails);
        $telephones = array_map('trim', $telephones);

        $maxContacts = max(count($noms), count($prenoms), count($fonctions));

        for ($i = 0; $i < $maxContacts; $i++) {
            $contact = [
                'nom' => $noms[$i] ?? '',
                'prenom' => $prenoms[$i] ?? '',
                'fonction' => $fonctions[$i] ?? '',
                'email' => $mails[$i] ?? '',
                'tel' => $telephones[$i] ?? ''
            ];

            if (!empty($contact['nom']) || !empty($contact['prenom']) || !empty($contact['email'])) {
                $contactsArray[] = $contact;
            }
        }

        return $contactsArray;
    }
}
