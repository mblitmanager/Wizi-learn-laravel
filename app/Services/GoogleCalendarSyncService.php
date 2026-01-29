<?php

namespace App\Services;

use App\Models\GoogleCalendar;
use App\Models\GoogleCalendarEvent;
use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleCalendarSyncService
{
    protected function getClient()
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(config('services.google.client_secret') ?: env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(config('services.google.redirect_uri') ?: env('GOOGLE_REDIRECT_URI'));
        $client->addScope(Calendar::CALENDAR_READONLY);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        return $client;
    }

    public function exchangeCode($code, $user)
    {
        $client = $this->getClient();
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \Exception("Erreur Google Exchange: " . json_encode($token));
        }

        $user->update([
            'google_access_token' => $token['access_token'],
            'google_refresh_token' => $token['refresh_token'] ?? $user->google_refresh_token,
            'google_token_expires_at' => now()->addSeconds($token['expires_in']),
        ]);

        return $token;
    }

    public function syncByUserId($userId)
    {
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->google_refresh_token) {
            return null;
        }

        $client = $this->getClient();
        $client->setAccessToken($user->google_access_token);

        if ($client->isAccessTokenExpired()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            if (isset($newToken['error'])) {
                Log::error("Impossible de rafraîchir le token pour le user $userId: " . json_encode($newToken));
                return null;
            }
            $user->update([
                'google_access_token' => $newToken['access_token'],
                'google_token_expires_at' => now()->addSeconds($newToken['expires_in']),
            ]);
        }

        return $this->syncAll($user->google_access_token, $user);
    }

    public function syncAll($accessToken, $user)
    {
        $client = new Client();
        $client->setAccessToken($accessToken);
        $service = new Calendar($client);

        $calendarList = $service->calendarList->listCalendarList();
        $calendarsSyncedCount = 0;
        $eventsSyncedCount = 0;

        foreach ($calendarList->getItems() as $calendarItem) {
            $googleCalendar = GoogleCalendar::updateOrCreate(
                ['google_id' => $calendarItem->getId(), 'user_id' => $user->id],
                [
                    'summary' => $calendarItem->getSummary(),
                    'description' => $calendarItem->getDescription(),
                    'background_color' => $calendarItem->getBackgroundColor(),
                    'foreground_color' => $calendarItem->getForegroundColor(),
                    'access_role' => $calendarItem->getAccessRole(),
                    'time_zone' => $calendarItem->getTimeZone(),
                    'synced_at' => now(),
                ]
            );
            $calendarsSyncedCount++;

            // Sync events for this calendar
            $eventsSyncedCount += $this->fetchEvents($calendarItem->getId(), $service, $googleCalendar);
        }

        return [
            'calendarsSynced' => $calendarsSyncedCount,
            'eventsSynced' => $eventsSyncedCount,
        ];
    }

    private function fetchEvents($calendarId, $service, $googleCalendar)
    {
        $timeMin = Carbon::now()->subMonths(1)->toRfc3339String();
        $timeMax = Carbon::now()->addMonths(3)->toRfc3339String();

        $optParams = [
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        try {
            $events = $service->events->listEvents($calendarId, $optParams);
            
            // Supprimer les anciens événements
            GoogleCalendarEvent::where('google_calendar_id', $googleCalendar->id)->delete();

            $syncedCount = 0;
            foreach ($events->getItems() as $event) {
                $start = $event->start->dateTime ?: $event->start->date;
                $end = $event->end->dateTime ?: $event->end->date;

                GoogleCalendarEvent::create([
                    'google_calendar_id' => $googleCalendar->id,
                    'google_id' => $event->getId(),
                    'summary' => $event->getSummary() ?: 'Sans titre',
                    'description' => $event->getDescription(),
                    'location' => $event->getLocation(),
                    'start' => Carbon::parse($start),
                    'end' => Carbon::parse($end),
                    'html_link' => $event->getHtmlLink(),
                    'hangout_link' => $event->getHangoutLink(),
                    'organizer' => $event->getOrganizer() ? ['email' => $event->getOrganizer()->getEmail()] : null,
                    'attendees' => array_map(fn($a) => ['email' => $a->getEmail()], (array)$event->getAttendees()),
                    'status' => $event->getStatus(),
                    'recurrence' => $event->getRecurrence(),
                    'event_type' => $event->getEventType(),
                ]);
                $syncedCount++;
            }
            return $syncedCount;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des événements pour le calendrier $calendarId: " . $e->getMessage());
            return 0;
        }
    }
}
