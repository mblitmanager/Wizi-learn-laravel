<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Participation;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $challenges = Challenge::latest()->paginate(10);
        return view('admin.challenges.index', compact('challenges'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.challenges.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'points' => 'required|integer|min:0',
        ]);

        Challenge::create($validated);

        return redirect()->route('challenges.index')->with('success', 'Défi créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Challenge $challenge)
    {
        return view('admin.challenges.show', compact('challenge'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Challenge $challenge)
    {
        return view('admin.challenges.edit', compact('challenge'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Challenge $challenge)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'points' => 'required|integer|min:0',
        ]);

        $challenge->update($validated);

        return redirect()->route('challenges.index')->with('success', 'Défi mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Challenge $challenge)
    {
        $challenge->delete();
        return redirect()->route('challenges.index')->with('success', 'Défi supprimé avec succès.');
    }
}
