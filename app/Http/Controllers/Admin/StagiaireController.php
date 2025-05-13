<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStagiaireRequest;
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
        $formations = Formation::all();
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

        $formations = Formation::all();
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


            foreach ($sheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];

                foreach ($cellIterator as $cell) {
                    $data[] = trim($cell->getValue());
                }

                if (count($data) < 8) continue;

                list($civilite, $tiers, $email, $telephone, $ville, $codePostal, $adresse, $dateNaissance) = $data;

                // Vérifie si l'utilisateur existe déjà par email
                if (User::where('email', $email)->exists()) {
                    $ignoredEmails[] = $email;
                    continue;
                }

                $np = $this->extraireNomPrenom($tiers);

                $user = User::create([
                    'name' => $np['prenom'] . ' ' . $np['nom'],
                    'email' => $email,
                    'password' => bcrypt('stagiaire123'),
                    'role' => 'stagiaire',
                ]);

                Stagiaire::create([
                    'civilite' => $civilite,
                    'prenom' => $np['prenom'],
                    'telephone' => $telephone,
                    'adresse' => $adresse,
                    'date_naissance' => $this->convertExcelDate($dateNaissance),
                    'ville' => $ville,
                    'code_postal' => $codePostal,
                    'user_id' => $user->id,
                    'role' => 'stagiaire',
                    'statut' => true,
                    'date_debut_formation' => null, // ou une valeur par défaut si dispo dans l'import
                    'date_inscription' => now(), // date d'import comme inscription
                ]);
            }


            return redirect()->route('stagiaires.index')->with('success', 'Importation réussie.')->with('ignored', $ignoredEmails);;
        } catch (\Exception $e) {
            return redirect()->route('stagiaires.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
