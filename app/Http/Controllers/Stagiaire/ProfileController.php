<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use App\Services\FormationService;
use App\Services\StagiaireService;

class ProfileController extends Controller
{
    protected $formationService;
    protected $stagiaireService;

    public function __construct(FormationService $formationService, StagiaireService $stagiaireService)
    {
        $this->stagiaireService = $stagiaireService;

        $this->formationService = $formationService;
    }
    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Stagiaire $stagiaire)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, Stagiaire $stagiaire)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Stagiaire $stagiaire)
    // {
    //     //
    // }

    
}
