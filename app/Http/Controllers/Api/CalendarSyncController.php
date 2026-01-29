<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\GoogleCalendar;
use App\Models\GoogleCalendarEvent;
use App\Models\User;

class CalendarSyncController extends Controller
{
    protected $syncService;

    public function __construct(\App\Services\GoogleCalendarSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function sync(Request $request)
    {
        $request->validate([
            'accessToken' => 'nullable|string',
            'authCode' => 'nullable|string',
            'userId' => 'nullable|string',
            // ...
        ]);

        $user = auth()->user() ?: User::find($request->input('userId'));

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        // Cas 1: Le front-end envoie un authCode (nouveau flux pour avoir le refresh_token)
        if ($request->filled('authCode')) {
            try {
                $token = $this->syncService->exchangeCode($request->input('authCode'), $user);
                $result = $this->syncService->syncAll($token['access_token'], $user);
                return response()->json([
                    'message' => 'Authentification réussie et synchronisation effectuée.',
                    'userId' => $user->id,
                    'calendarsSynced' => $result['calendarsSynced'],
                    'eventsSynced' => $result['eventsSynced'],
                ]);
            } catch (\Exception $e) {
                Log::error("Erreur exchangeCode Laravel: " . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        // Cas 2: Le front-end envoie un accessToken (flux rapide)
        if ($request->filled('accessToken')) {
            try {
                $result = $this->syncService->syncAll($request->input('accessToken'), $user);
                return response()->json([
                    'message' => 'Synchronisation effectuée par le serveur Laravel.',
                    'userId' => $user->id,
                    'calendarsSynced' => $result['calendarsSynced'],
                    'eventsSynced' => $result['eventsSynced'],
                ]);
            } catch (\Exception $e) {
                Log::error("Erreur sync Laravel: " . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        $calendarsData = $request->input('calendars') ?: [];
        $eventsData = $request->input('events') ?: [];

        Log::info("Synchronisation Google Calendar reçue pour l'utilisateur: {$user->id}");

        $calendarsSyncedCount = 0;
        $eventsSyncedCount = 0;

        foreach ($calendarsData as $calendarData) {
            $calendar = GoogleCalendar::updateOrCreate(
                ['google_id' => $calendarData['googleId'], 'user_id' => $user->id],
                [
                    'summary' => $calendarData['summary'],
                    'description' => $calendarData['description'] ?? null,
                    'background_color' => $calendarData['backgroundColor'] ?? null,
                    'foreground_color' => $calendarData['foregroundColor'] ?? null,
                    'access_role' => $calendarData['accessRole'] ?? null,
                    'time_zone' => $calendarData['timeZone'] ?? null,
                    'synced_at' => now(),
                ]
            );
            $calendarsSyncedCount++;

            GoogleCalendarEvent::where('google_calendar_id', $calendar->id)->delete();

            foreach ($eventsData as $eventData) {
                if ($eventData['calendarId'] === $calendar->google_id) {
                    GoogleCalendarEvent::create([
                        'google_calendar_id' => $calendar->id,
                        'google_id' => $eventData['googleId'],
                        'summary' => $eventData['summary'] ?? null,
                        'description' => $eventData['description'] ?? null,
                        'location' => $eventData['location'] ?? null,
                        'start' => $eventData['start'],
                        'end' => $eventData['end'],
                        'html_link' => $eventData['htmlLink'] ?? null,
                        'hangout_link' => $eventData['hangoutLink'] ?? null,
                        'organizer' => $eventData['organizer'] ?? null,
                        'attendees' => $eventData['attendees'] ?? null,
                        'status' => $eventData['status'] ?? null,
                        'recurrence' => $eventData['recurrence'] ?? null,
                        'event_type' => $eventData['eventType'] ?? null,
                    ]);
                    $eventsSyncedCount++;
                }
            }
        }

        return response()->json([
            'message' => 'Données Google Calendar synchronisées avec succès sur Laravel (Legacy).',
            'userId' => $user->id,
            'calendarsSynced' => $calendarsSyncedCount,
            'eventsSynced' => $eventsSyncedCount,
        ]);
    }
}
