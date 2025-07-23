<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogueFormationRequest;
use App\Models\Formation;
use App\Services\CatalogueFormationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class CatalogueFormationController extends Controller
{

    protected $catalogueFormationService;
    protected $notificationService;
    public function __construct(CatalogueFormationService $catalogueFormationService,NotificationService $notificationService)
    {
        $this->catalogueFormationService = $catalogueFormationService;
        $this->notificationService = $notificationService;
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

        $this->catalogueFormationService->create($validated);

        // Notification uniquement aux stagiaires rattachés à la formation du catalogue
        if (isset($validated['formation_id'])) {
            $catalogueIds = [null];
            if (isset($validated['formation_id'])) {
                $catalogueIds = \App\Models\CatalogueFormation::where('formation_id', $validated['formation_id'])->pluck('id');
            }
            $stagiaires = \App\Models\Stagiaire::whereHas('catalogue_formations', function ($q) use ($catalogueIds) {
                $q->whereIn('catalogue_formation_id', $catalogueIds);
            })->with('user')->get();
            foreach ($stagiaires as $stagiaire) {
                if ($stagiaire->user) {
                    $title = 'Catalogue de formation';
                    $body = 'Un nouveau catalogue de formation a été ajouté ou mis à jour.';
                    $iconUrl = url('media/wizi.png'); // Assure-toi que l'image est bien à cet emplacement
                    $data = [
                        'type' => 'formation',
                        'icon' => $iconUrl
                    ];
                    $this->notificationService->sendFcmToUser(
                        $stagiaire->user,
                        $title,
                        $body,
                        $data
                    );
                    \App\Models\Notification::create([
                        'user_id' => $stagiaire->user->id,
                        'type' => $data['type'],
                        'title' => $title,
                        'message' => $body,
                        'data' => $data,
                        'read' => false,
                    ]);
                }
            }
        }

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

        $this->catalogueFormationService->update($id, $validated);

        // Notification FCM + historique lors de la mise à jour du catalogue
        if (isset($validated['formation_id'])) {
            $catalogueIds = [null];
            if (isset($validated['formation_id'])) {
                $catalogueIds = \App\Models\CatalogueFormation::where('formation_id', $validated['formation_id'])->pluck('id');
            }
            $stagiaires = \App\Models\Stagiaire::whereHas('catalogue_formations', function ($q) use ($catalogueIds) {
                $q->whereIn('catalogue_formation_id', $catalogueIds);
            })->with('user')->get();
            // Get the catalogue de formation title
            $catalogue = $this->catalogueFormationService->show($id);
            $formationTitre = ($catalogue && isset($catalogue->titre)) ? $catalogue->titre : '';
            foreach ($stagiaires as $stagiaire) {
                if ($stagiaire->user) {
                    $title = 'Formation mis à jour : ' . $formationTitre;
                    $body = 'Les détails de la formation "' . $formationTitre . '" a été mis à jour.';
                    $data = ['type' => 'formation'];
                    $this->notificationService->sendFcmToUser(
                        $stagiaire->user,
                        $title,
                        $body,
                        $data
                    );
                    \App\Models\Notification::create([
                        'user_id' => $stagiaire->user->id,
                        'type' => $data['type'],
                        'title' => $title,
                        'message' => $body,
                        'data' => $data,
                        'read' => false,
                    ]);
                }
            }
        }

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
