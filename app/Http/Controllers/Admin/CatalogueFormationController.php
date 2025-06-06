<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogueFormationRequest;
use App\Models\Formation;
use App\Services\CatalogueFormationService;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

class CatalogueFormationController extends Controller
{
    protected $catalogueFormationService;
    protected $userController;

    public function __construct(
        CatalogueFormationService $catalogueFormationService,
        UserController $userController
    ) {
        $this->catalogueFormationService = $catalogueFormationService;
        $this->userController = $userController;
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
            $file->move(public_path('media'), $filename);
            $validated['image_url'] = 'media/' . $filename;
            $validated['file_type'] = $file->getClientMimeType();
        }

        if ($request->hasFile('cursus_pdf')) {
            $pdfFile = $request->file('cursus_pdf');
            $pdfName = time() . '_' . $pdfFile->getClientOriginalName();
            $pdfFile->move(public_path('pdfs'), $pdfName);
            $validated['cursus_pdf'] = 'pdfs/' . $pdfName;
        }

        $catalogueFormation = $this->catalogueFormationService->create($validated);

        // Envoyer une notification pour la nouvelle formation
        $this->userController->notifyCatalogueFormationUpdated($catalogueFormation);

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
            $file->move(public_path('media'), $filename);
            $validated['image_url'] = 'media/' . $filename;
            $validated['file_type'] = $file->getClientMimeType();
        }

        if ($request->hasFile('cursus_pdf')) {
            $pdfFile = $request->file('cursus_pdf');
            $pdfName = time() . '_' . $pdfFile->getClientOriginalName();
            $pdfFile->move(public_path('pdfs'), $pdfName);
            $validated['cursus_pdf'] = 'pdfs/' . $pdfName;
        }

        $catalogueFormation = $this->catalogueFormationService->update($id, $validated);

        // Envoyer une notification pour la mise à jour de la formation
        $this->userController->notifyCatalogueFormationUpdated($catalogueFormation);

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
        try {
            $catalogueFormation = $this->catalogueFormationService->show($id);

            if (!$catalogueFormation || !$catalogueFormation->cursus_pdf) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier PDF n\'existe pas.'
                ], 404);
            }

            // Vérifier si le fichier existe dans le dossier public
            $filePath = base_path('public/' . $catalogueFormation->cursus_pdf);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier PDF n\'a pas été trouvé sur le serveur.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => asset($catalogueFormation->cursus_pdf),
                    'filename' => 'cursus_' . strtoupper($catalogueFormation->titre) . '.pdf'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du téléchargement du PDF.'
            ], 500);
        }
    }
}
