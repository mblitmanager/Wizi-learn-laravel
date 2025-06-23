<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\Request;
use App\Events\DailyNotificationEvent;
use App\Models\User;
use App\Models\Stagiaire;

class DailyNotificationController extends Controller
{
    public function send(Request $request)
    {
        // Heure française (Europe/Paris)
        $now = now()->setTimezone('Europe/Paris');
        $today = $now->toDateString();
        $targetTime = $now->copy()->setTime(9, 0, 0);

        // Clé de cache pour éviter les doublons
        $cacheKey = 'daily_notification_sent_' . $today;

        // Récupérer les stagiaires dont la date de début de formation est dans 7 jours ou moins
        $stagiaires = Stagiaire::whereDate('date_debut_formation', '>=', $today)
            ->whereDate('date_debut_formation', '<=', $now->copy()->addDays(7)->toDateString())
            ->with('user')
            ->get();

        foreach ($stagiaires as $stagiaire) {
            if ($stagiaire->user && $stagiaire->user->fcm_token) {
                // Envoi direct FCM
                try {
                    $serviceAccountPath = storage_path('app/firebase-service-account.json');
                    $projectId = env('FIREBASE_PROJECT_ID');
                    if (!file_exists($serviceAccountPath) || !$projectId) {
                        throw new \Exception('Service account file or project ID missing');
                    }
                    $client = new \Google_Client();
                    $client->setAuthConfig($serviceAccountPath);
                    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                    $client->fetchAccessTokenWithAssertion();
                    $accessToken = $client->getAccessToken()['access_token'];
                    $data = [
                        'message' => [
                            'token' => $stagiaire->user->fcm_token,
                            'notification' => [
                                'title' => 'Rappel de formation',
                                'body' => 'Votre formation commence bientôt !',
                            ],
                        ],
                    ];
                    $httpClient = new \GuzzleHttp\Client();
                    $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
                    $response = $httpClient->post($url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => $data,
                    ]);
                    // Optionally log or handle $response
                } catch (\Exception $e) {
                    // Optionally log error
                }
            }
        }
        // Marquer comme envoyé pour aujourd'hui (expire dans 24h)
        Cache::put($cacheKey, true, now()->addDay());
        return response()->json(['status' => 'sent', 'count' => $stagiaires->count()]);
    }
}
