<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PoleRelationClientRequest;
use App\Models\Stagiaire;
use App\Services\PoleRelationClientService;
use Illuminate\Http\Request;

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
        return view('admin.pole_relation_client.create',compact('stagiaires'));
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
}
