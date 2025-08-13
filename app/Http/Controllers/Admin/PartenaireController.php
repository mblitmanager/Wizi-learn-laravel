<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use App\Models\Stagiaire;
use Illuminate\Http\Request;

class PartenaireController extends Controller
{
    public function show($id)
    {
        $partenaire = Partenaire::with('stagiaires.user')->findOrFail($id);
        return view('admin.partenaires.show', compact('partenaire'));
    }

    public function index()
    {
        $partenaires = Partenaire::with('stagiaires')->get();
        return view('admin.partenaires.index', compact('partenaires'));
    }

    public function create()
    {
        $stagiaires = Stagiaire::all();
        return view('admin.partenaires.create', compact('stagiaires'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'identifiant' => 'required|unique:partenaires',
            'adresse' => 'required',
            'ville' => 'required',
            'departement' => 'required',
            'code_postal' => 'required',
            'type' => 'required',
            'stagiaires' => 'array',
            'logo' => 'nullable|image|max:2048',
            'contacts' => 'nullable|array|max:3',
            'contacts.*.nom' => 'required_with:contacts|string|max:255',
            'contacts.*.prenom' => 'required_with:contacts|string|max:255',
            'contacts.*.fonction' => 'nullable|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.tel' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = uniqid('logo_') . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('partenaires'), $logoName);
            $data['logo'] = 'partenaires/' . $logoName;
        }

        // Nettoyer contacts: retirer entrées vides
        if (!empty($data['contacts'])) {
            $data['contacts'] = array_values(array_filter($data['contacts'], function ($c) {
                return !empty($c['nom']) || !empty($c['prenom']) || !empty($c['email']) || !empty($c['tel']);
            }));
        }

        $partenaire = Partenaire::create($data);
        if (!empty($data['stagiaires'])) {
            $partenaire->stagiaires()->sync($data['stagiaires']);
        }
        return redirect()->route('partenaires.index')->with('success', 'Partenaire créé avec succès');
    }

    public function edit($id)
    {
        $partenaire = Partenaire::with('stagiaires')->findOrFail($id);
        $stagiaires = Stagiaire::all();
        return view('admin.partenaires.edit', compact('partenaire', 'stagiaires'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'identifiant' => 'required|unique:partenaires,identifiant,' . $id,
            'adresse' => 'required',
            'ville' => 'required',
            'departement' => 'required',
            'code_postal' => 'required',
            'type' => 'required',
            'stagiaires' => 'array',
            'logo' => 'nullable|image|max:2048',
            'contacts' => 'nullable|array|max:3',
            'contacts.*.nom' => 'required_with:contacts|string|max:255',
            'contacts.*.prenom' => 'required_with:contacts|string|max:255',
            'contacts.*.fonction' => 'nullable|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.tel' => 'nullable|string|max:50',
        ]);

        $partenaire = Partenaire::findOrFail($id);
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = uniqid('logo_') . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('partenaires'), $logoName);
            $data['logo'] = 'partenaires/' . $logoName;
        }

        if (!empty($data['contacts'])) {
            $data['contacts'] = array_values(array_filter($data['contacts'], function ($c) {
                return !empty($c['nom']) || !empty($c['prenom']) || !empty($c['email']) || !empty($c['tel']);
            }));
        }

        $partenaire->update($data);
        if (!empty($data['stagiaires'])) {
            $partenaire->stagiaires()->sync($data['stagiaires']);
        } else {
            $partenaire->stagiaires()->detach();
        }
        return redirect()->route('partenaires.index')->with('success', 'Partenaire mis à jour avec succès');
    }

    public function destroy($id)
    {
        $partenaire = Partenaire::findOrFail($id);
        $partenaire->stagiaires()->detach();
        $partenaire->delete();
        return redirect()->route('partenaires.index')->with('success', 'Partenaire supprimé avec succès');
    }
}
