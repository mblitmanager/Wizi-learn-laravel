<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStagiaireRequest;
use App\Models\Commercial;
use App\Models\Formateur;
use App\Models\Formation;
use App\Services\StagiaireService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
}
