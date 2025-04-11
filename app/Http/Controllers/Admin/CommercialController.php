<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommmercialStoreRequest;
use App\Services\CommercialService;
use Illuminate\Http\Request;

class CommercialController extends Controller
{
    protected $commercialsService;

    public function __construct(CommercialService $commercialsService)
    {
        $this->commercialsService = $commercialsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commercial = $this->commercialsService->list();
        return view('admin.commercial.index', compact('commercial'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.commercial.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommmercialStoreRequest $request)
    {
        $this->commercialsService->create($request->validated());

        return redirect()->route('commercials.index')
            ->with('success', 'Le commercials a été créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $commercial = $this->commercialsService->show($id);
        return view('admin.commercial.edit', compact('commercial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommmercialStoreRequest $request, string $id)
    {
        $this->commercialsService->update($id, $request->validated());

        return redirect()->route('commercials.index')
            ->with('success', 'Le commercials a été mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
