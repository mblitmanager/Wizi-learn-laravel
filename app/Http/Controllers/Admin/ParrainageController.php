<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ParrainageController extends Controller
{
    public function index()
    {
        // Récupère tous les parrains avec le compte de leurs filleuls
        $parrains = User::whereHas('parrainages')
            ->withCount('parrainages')
            ->orderBy('parrainages_count', 'desc')
            ->get();
        return view('admin.parrainage.index', compact('parrains'));
    }

    public function show($id)
    {
        // Récupère le parrain spécifique avec ses filleuls
        $parrain = User::with('parrainages.filleul')
            ->findOrFail($id);

        return view('admin.parrainage.show', compact('parrain'));
    }
}
