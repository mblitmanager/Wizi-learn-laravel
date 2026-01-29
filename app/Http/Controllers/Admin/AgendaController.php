<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleCalendar;
use App\Models\GoogleCalendarEvent;
use App\Models\User;
use App\Services\GoogleCalendarSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AgendaController extends Controller
{
    protected $syncService;

    public function __construct(GoogleCalendarSyncService $syncService)
    {
        $this->syncService = $syncService;
    }
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

    public function sync(Request $request)
    {
        $user = Auth::user();
        $authCode = $request->input('authCode');

        try {
            if ($authCode) {
                // Connection/Sync for the current user
                $this->syncService->exchangeCode($authCode, $user);
                $result = $this->syncService->syncByUserId($user->id);
                return response()->json([
                    'message' => 'Synchronisation de votre compte réussie.',
                    'info' => $result
                ]);
            } else {
                // Global sync for admin
                $isAdmin = in_array(strtolower($user->role), ['administrateur', 'admin']);
                if (!$isAdmin) {
                    return response()->json(['message' => 'Seul l\'administrateur peut lancer la synchronisation globale.'], 403);
                }

                $users = User::whereNotNull('google_refresh_token')->get();
                $results = [];
                foreach ($users as $u) {
                    try {
                        $res = $this->syncService->syncByUserId($u->id);
                        $results[] = ['userId' => $u->id, 'status' => 'success', 'info' => $res];
                    } catch (\Exception $e) {
                        $results[] = ['userId' => $u->id, 'status' => 'error', 'message' => $e->getMessage()];
                    }
                }

                return response()->json([
                    'message' => 'Synchronisation globale terminée.',
                    'results' => $results
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
