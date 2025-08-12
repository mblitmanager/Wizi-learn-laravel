<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormationRequest;
use App\Services\FormationService;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    protected $formationService;

    public function __construct(FormationService $formationService)
    {
        $this->formationService = $formationService;
    }

    public function index()
    {
        $formations = $this->formationService->getAll();
        return view('admin.formations.index', compact('formations'));
    }

    public function create()
    {
        return view('admin.formations.create');
    }

    public function store(StoreFormationRequest $request)
    {
        $this->formationService->store($request->validated());
        return redirect()->route('formations.index')->with('success', 'Formation créée avec succès !');
    }

    public function show($id)
    {
        $formation = $this->formationService->getById($id);
        if (!$formation) return redirect()->route('formations.index')->with('error', 'Formation non trouvée.');
        return view('admin.formations.show', compact('formation'));
    }

    public function edit($id)
    {
        $formation = $this->formationService->getById($id);
        if (!$formation) return redirect()->route('formations.index')->with('error', 'Formation non trouvée.');
        return view('admin.formations.edit', compact('formation'));
    }

    public function update(StoreFormationRequest $request, $id)
    {
        $updated = $this->formationService->update($id, $request->validated());

        return $updated
            ? redirect()->route('formations.index')->with('success', 'Formation mise à jour avec succès !')
            : redirect()->route('formations.index')->with('error', 'Formation non trouvée.');
    }

    public function destroy($id)
    {
        $deleted = $this->formationService->destroy($id);

        return $deleted
            ? redirect()->route('formations.index')->with('success', 'Formation supprimée avec succès !')
            : redirect()->route('formations.index')->with('error', 'Formation non trouvée.');
    }

    /**
     * Dupliquer une formation
     */
    public function duplicate($id)
    {
        $formation = $this->formationService->getById($id);
        if (!$formation) {
            return redirect()->route('formations.index')->with('error', 'Formation non trouvée.');
        }
        $newData = $formation->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at']);
        $newData['nom'] = $formation->nom . ' (copie)';
        $newFormation = $this->formationService->store($newData);
        return redirect()->route('formations.edit', $newFormation->id)
            ->with('success', 'Formation dupliquée avec succès.');
    }
}
