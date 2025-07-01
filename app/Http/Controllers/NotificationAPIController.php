<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class NotificationAPIController extends Controller
{
    // Envoie une notification via Pusher et FCM
    public function send(Request $request)
    {

        Log::info('NotificationController@send - Début', $request->all());
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        Log::info('NotificationController@send - Après validation');

        $user = User::findOrFail($request->user_id);
        $title = $request->title;
        $body = $request->body;

        Log::info('NotificationController@send - Utilisateur trouvé', ['user' => $user->id, 'fcm_token' => $user->fcm_token]);

        // 1. Pusher (broadcast Laravel)
        // try {
        //     broadcast(new \App\Events\NotificationEvent($user->id, $title, $body))->toOthers();
        //     Log::info('NotificationController@send - Pusher broadcast OK');
        // } catch (\Exception $e) {
        //     Log::error('NotificationController@send - Erreur Pusher: ' . $e->getMessage());
        // }

        // 2. FCM HTTP v1
        $fcmResponse = null;
        if ($user->fcm_token) {
            try {
                $serviceAccountPath = storage_path('app/firebase-service-account.json');
                $projectId = env('FIREBASE_PROJECT_ID');
                if (!file_exists($serviceAccountPath) || !$projectId) {
                    throw new \Exception('Service account file or project ID missing');
                }

                // Get Google access token
                $client = new \Google_Client();
                $client->setAuthConfig($serviceAccountPath);
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                $client->fetchAccessTokenWithAssertion();
                $accessToken = $client->getAccessToken()['access_token'];

                $data = [
                    'message' => [
                        'token' => $user->fcm_token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
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
                $fcmResponse = json_decode($response->getBody(), true);
                Log::info('NotificationController@send - FCM v1 OK', ['fcm' => $fcmResponse]);
            } catch (\Exception $e) {
                Log::error('NotificationController@send - Erreur FCM v1: ' . $e->getMessage());
                $fcmResponse = ['error' => $e->getMessage()];
            }
        } else {
            Log::warning('NotificationController@send - Pas de fcm_token pour cet utilisateur');
        }

        Log::info('NotificationController@send - Fin');

        return response()->json([
            'message' => 'Notification envoyée',
            'fcm' => $fcmResponse
        ]);
    }
}
