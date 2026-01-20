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
    public function sync(Request $request)
    {
        // Valider les données entrantes
        $request->validate([
            'userId' => 'required|string',
            'calendars' => 'required|array',
            'events' => 'required|array',
            'calendars.*.googleId' => 'required|string',
            'calendars.*.summary' => 'required|string',
            'calendars.*.description' => 'nullable|string',
            'calendars.*.backgroundColor' => 'nullable|string',
            'calendars.*.foregroundColor' => 'nullable|string',
            // Ajoutez d'autres validations pour les champs de calendrier si nécessaire
            'events.*.googleId' => 'required|string',
            'events.*.calendarId' => 'required|string',
            'events.*.summary' => 'nullable|string',
            'events.*.description' => 'nullable|string',
            'events.*.location' => 'nullable|string',
            'events.*.start' => 'required|string',
            'events.*.end' => 'required|string',
            'events.*.htmlLink' => 'nullable|string',
            'events.*.hangoutLink' => 'nullable|string',
            'events.*.organizer' => 'nullable|array',
            'events.*.attendees' => 'nullable|array',
            // Ajoutez d'autres validations pour les champs d'événement si nécessaire
        ]);

        $userId = $request->input('userId');
        $calendarsData = $request->input('calendars');
        $eventsData = $request->input('events');

        // Assurez-vous que l'utilisateur existe
        $user = User::where('id', $userId)->first();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        Log::info("Synchronisation Google Calendar reçue pour l'utilisateur: {$userId}");

        $calendarsSyncedCount = 0;
        $eventsSyncedCount = 0;

        foreach ($calendarsData as $calendarData) {
            $calendar = GoogleCalendar::updateOrCreate(
                ['google_id' => $calendarData['googleId'], 'user_id' => $userId],
                [
                    'summary' => $calendarData['summary'],
                    'description' => $calendarData['description'] ?? null,
                    'background_color' => $calendarData['backgroundColor'] ?? null,
                    'foreground_color' => $calendarData['foregroundColor'] ?? null,
                    'access_role' => $calendarData['accessRole'] ?? null, // Assurez-vous que ce champ est envoyé par le front-end
                    'time_zone' => $calendarData['timeZone'] ?? null,     // Assurez-vous que ce champ est envoyé par le front-end
                    'synced_at' => now(),
                ]
            );
            $calendarsSyncedCount++;

            // Supprimer tous les événements existants pour ce calendrier et cette date
            // Alternativement, vous pouvez faire une mise à jour intelligente ou supprimer seulement les événements non présents
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
                        'status' => $eventData['status'] ?? null, // Assurez-vous que ce champ est envoyé par le front-end
                        'recurrence' => $eventData['recurrence'] ?? null, // Assurez-vous que ce champ est envoyé par le front-end
                        'event_type' => $eventData['eventType'] ?? null, // Assurez-vous que ce champ est envoyé par le front-end
                    ]);
                    $eventsSyncedCount++;
                }
            }
        }

        return response()->json([
            'message' => 'Données Google Calendar synchronisées avec succès sur Laravel.',
            'userId' => $userId,
            'calendarsSynced' => $calendarsSyncedCount,
            'eventsSynced' => $eventsSyncedCount,
        ]);
    }
}
