<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormateurStoreRequest;
use App\Models\Formation;
use App\Services\FormateurService;
use Illuminate\Http\Request;

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
        return view('admin.formateur.edit', compact('formateur'));
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
}
