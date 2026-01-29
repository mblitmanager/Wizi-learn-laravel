<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleCalendar;
use App\Models\GoogleCalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AgendaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = strtolower($user->role);

        if (!in_array($role, ['administrateur', 'admin', 'formateur', 'formatrice'])) {
            abort(403, 'Accès non autorisé');
        }
        
        // Generate JWT for Node.js API calls
        $token = JWTAuth::fromUser($user);
        
        // Get calendars for the user
        $calendars = GoogleCalendar::where('user_id', $user->id)->get();
        
        return view('admin.agenda.index', compact('calendars', 'token'));
    }

    public function getEvents(Request $request)
    {
        $user = Auth::user();
        
        $events = GoogleCalendarEvent::whereHas('googleCalendar', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->get()
        ->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->summary,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end->toIso8601String(),
                'description' => $event->description,
                'location' => $event->location,
                'color' => $event->googleCalendar->background_color ?? '#3788d8',
            ];
        });

        return response()->json($events);
    }
}
