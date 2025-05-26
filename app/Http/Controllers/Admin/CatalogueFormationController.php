<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogueFormationRequest;
use App\Models\Formation;
use App\Services\CatalogueFormationService;
use Illuminate\Http\Request;

class CatalogueFormationController extends Controller
{

    protected $catalogueFormationService;
    public function __construct(CatalogueFormationService $catalogueFormationService)
    {
        $this->catalogueFormationService = $catalogueFormationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $catalogueFormations = $this->catalogueFormationService->list();
        return view('admin.catalogue_formation.index', compact('catalogueFormations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formations = Formation::all();
        return view('admin.catalogue_formation.create', compact('formations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CatalogueFormationRequest $request)
    {
        $validated = $request->validated();


        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('media', $filename, 'public');

            $validated['image_url'] = $path; // <-- CORRECTION ICI
            $validated['file_type'] = $file->getClientMimeType();
        }

        $this->catalogueFormationService->create($validated);

        return redirect()->route('catalogue_formation.index')
            ->with('success', 'Le catalogue de formation a été créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $catalogueFormations = $this->catalogueFormationService->show($id);
        $formation = Formation::find($catalogueFormations->formation_id);
        return view('admin.catalogue_formation.show', compact('catalogueFormations', 'formation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $catalogueFormation = $this->catalogueFormationService->show($id);
        $formations = Formation::all();
        return view('admin.catalogue_formation.edit', compact('catalogueFormation', 'formations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CatalogueFormationRequest $request, string $id)
    {
        $validated = $request->validated();

        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('media', $filename, 'public');

            $validated['image_url'] = $path; // <-- CORRECTION ICI
            $validated['file_type'] = $file->getClientMimeType();
        }

        $this->catalogueFormationService->update($id, $validated);
        return redirect()->route('catalogue_formation.index')
            ->with('success', 'Le catalogue de formation a été mis à jour avec succès.');
    }

    /**
     * Dupliquer un catalogue de formation
     */
    public function duplicate($id)
    {
        $catalogue = $this->catalogueFormationService->show($id);
        if (!$catalogue) {
            return redirect()->route('catalogue_formation.index')->with('error', 'Catalogue non trouvé.');
        }
        $newData = $catalogue->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at']);
        $newData['titre'] = $catalogue->titre . ' (copie)';
        $newCatalogue = $this->catalogueFormationService->create($newData);
        return redirect()->route('catalogue_formation.edit', $newCatalogue->id)
            ->with('success', 'Catalogue de formation dupliqué avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->catalogueFormationService->delete($id);
        return redirect()->route('catalogue_formation.index')
            ->with('success', 'Le catalogue de formation a été supprimé avec succès.');
    }

    /**
     * Télécharger le PDF du cursus
     */
    public function downloadPdf($id)
    {
        $catalogueFormation = $this->catalogueFormationService->show($id);

        if (!$catalogueFormation || !$catalogueFormation->cursus_pdf) {
            return redirect()->back()->with('error', 'Le fichier PDF n\'existe pas.');
        }

        $path = storage_path('app/public/' . $catalogueFormation->cursus_pdf);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Le fichier PDF n\'a pas été trouvé sur le serveur.');
        }

        return response()->download($path, 'cursus_' . $catalogueFormation->titre . '.pdf');
    }
}
