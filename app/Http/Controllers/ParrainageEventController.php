<?php

namespace App\Http\Controllers;

use App\Models\ParrainageEvent;
use Illuminate\Http\Request;

class ParrainageEventController extends Controller
{
    public function index()
    {
        $events = ParrainageEvent::latest()->paginate(10);
        return view('admin.parrainage_events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.parrainage_events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'status'=> 'required',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        ParrainageEvent::create($request->all());

        return redirect()->route('parrainage_events.index')
            ->with('success', 'Événement de parrainage créé avec succès.');
    }

    public function show(ParrainageEvent $parrainageEvent)
    {
        return view('admin.parrainage_events.show', compact('parrainageEvent'));
    }

    public function edit(ParrainageEvent $parrainageEvent)
    {
        return view('admin.parrainage_events.edit', compact('parrainageEvent'));
    }

    public function update(Request $request, ParrainageEvent $parrainageEvent)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'status'=> 'required',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        $parrainageEvent->update($request->all());

        return redirect()->route('parrainage_events.index')
            ->with('success', 'Événement de parrainage mis à jour.');
    }

    public function destroy(ParrainageEvent $parrainageEvent)
    {
        $parrainageEvent->delete();

        return redirect()->route('parrainage_events.index')
            ->with('success', 'Événement de parrainage supprimé.');
    }
}
