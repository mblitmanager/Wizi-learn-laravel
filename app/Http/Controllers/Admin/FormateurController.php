<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormateurStoreRequest;
use App\Models\Formateur;
use App\Models\Formation;
use App\Models\User;
use App\Services\FormateurService;
use Illuminate\Http\Request;
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
        $formations = Formation::all();

        return view('admin.formateur.create', compact('formations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormateurStoreRequest $request)
    {
        $this->formateurService->create($request->validated());

        return redirect()->route('formateur.index')
            ->with('success', 'Le formateur a été créé avec succès.');
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
        $formations = Formation::all();
        return view('admin.formateur.edit', compact('formateur', 'formations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FormateurStoreRequest $request, string $id)
    {
        $this->formateurService->update($id, $request->validated());

        return redirect()->route('formateur.index')
            ->with('success', 'Le formateur a été mis à jour avec succès.');
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
        $parts = preg_split('/\s+/', trim($fullName));

        if (is_numeric($parts[0])) {
            array_shift($parts);
        }

        $nom = [];
        $prenom = [];

        foreach ($parts as $part) {
            if (mb_strtoupper($part, 'UTF-8') === $part) {
                $nom[] = ucfirst(strtolower($part));
            } else {
                $prenom[] = ucfirst(strtolower($part));
            }
        }

        return [
            'nom' => implode(' ', $nom),
            'prenom' => implode(' ', $prenom),
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

            foreach ($sheet->getRowIterator(2) as $row) {
                $cell = $row->getCellIterator()->current();
                $consultantsCell = trim($cell->getValue());

                if (empty($consultantsCell)) continue;

                $consultants = $this->splitConsultants($consultantsCell);

                foreach ($consultants as $consultant) {
                    $np = $this->extraireNomPrenom($consultant);

                    $email = strtolower(str_replace(' ', '.', $np['prenom']) . '.' . strtolower($np['nom'])) . '@example.com';

                    if (User::where('email', $email)->exists()) {
                        $ignoredEmails[] = $email;
                        continue;
                    }

                    $user = User::create([
                        'name' => $np['prenom'] . ' ' . $np['nom'],
                        'email' => $email,
                        'password' => bcrypt('formateur123'),
                        'role' => 'Formateur',
                    ]);

                    Formateur::create([
                        'prenom' => $np['prenom'],
                        'user_id' => $user->id,
                        'role' => 'Formateur',
                    ]);
                }
            }

            return redirect()->route('formateur.index')
                ->with('success', 'Importation réussie.')
                ->with('ignored', $ignoredEmails);
        } catch (\Exception $e) {
            return redirect()->route('formateur.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
