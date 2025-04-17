<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\AgendaService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    protected $agendaService;

    public function __construct(AgendaService $agendaService)
    {
        $this->agendaService = $agendaService;
    }

    public function getAgenda()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $agenda = $this->agendaService->getStagiaireAgenda($user->id);
            return response()->json($agenda);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function exportAgenda()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $icsContent = $this->agendaService->exportAgendaToICS($user->id);
            return response($icsContent)
                ->header('Content-Type', 'text/calendar')
                ->header('Content-Disposition', 'attachment; filename="agenda.ics"');
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getNotifications()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $notifications = $this->agendaService->getStagiaireNotifications($user->id);
            return response()->json($notifications);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
} 