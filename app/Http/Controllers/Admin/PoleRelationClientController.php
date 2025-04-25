<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PoleRelationClientRequest;
use App\Models\PoleRelationClient;
use App\Models\Stagiaire;
use App\Models\User;
use App\Services\PoleRelationClientService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PoleRelationClientController extends Controller
{
    protected $polerelationClientRepository;

    public function __construct(PoleRelationClientService $polerelationClientRepository)
    {
        $this->polerelationClientRepository = $polerelationClientRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $poleRelationClients = $this->polerelationClientRepository->all();
        return view('admin.pole_relation_client.index', compact('poleRelationClients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stagiaires = Stagiaire::all();
        return view('admin.pole_relation_client.create', compact('stagiaires'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PoleRelationClientRequest $request)
    {
        $this->polerelationClientRepository->create($request->validated());
        return redirect()->route('pole_relation_clients.index')
            ->with('success', 'Le pole relation client a été créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $poleRelationClient = $this->polerelationClientRepository->find($id);
        return view('admin.pole_relation_client.show', compact('poleRelationClient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $poleRelationClient = $this->polerelationClientRepository->find($id);
        $stagiaires = Stagiaire::all();
        return view('admin.pole_relation_client.edit', compact('poleRelationClient', 'stagiaires'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->polerelationClientRepository->update($id, $request->all());
        return redirect()->route('pole_relation_clients.index')
            ->with('success', 'Le pole relation client a été mis à jour avec succès.');
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

                if (empty($consultantsCell))
                    continue;

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
                        'password' => bcrypt('consultant123'),
                        'role' => 'pole relation client',
                    ]);

                    PoleRelationClient::create([
                        'prenom' => $np['prenom'],
                        'user_id' => $user->id,
                        'role' => 'pole relation client',
                    ]);
                }
            }
            if (!empty($ignoredEmails)) {
                return redirect()->route('pole_relation_clients.index')
                    ->with('success', 'Importation réussie. Certains emails ont été ignorés.')
                    ->with('ignoredEmails', $ignoredEmails);
            }

            return redirect()->route('pole_relation_clients.index')
                ->with('success', 'Importation réussie.')
            ;
        } catch (\Exception $e) {
            return redirect()->route('pole_relation_clients.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
