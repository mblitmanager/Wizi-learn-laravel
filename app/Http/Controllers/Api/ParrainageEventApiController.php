<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParrainageEvent;
use Illuminate\Http\Request;

class ParrainageEventApiController extends Controller
{
    /**
     * Retourner la liste des événements de parrainage (API).
     */
    public function index()
    {
        try {
            $events = ParrainageEvent::orderBy('date_debut', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des événements',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
