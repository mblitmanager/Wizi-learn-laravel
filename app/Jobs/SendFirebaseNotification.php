<?php

namespace App\Jobs;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendFirebaseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Event $event
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/v1/projects/wizi-learn/messages:send', [
                'message' => [
                    'topic' => $this->event->topic,
                    'notification' => [
                        'title' => $this->event->title,
                        'body' => $this->event->message,
                    ],
                    'data' => $this->event->data ?? [],
                    'webpush' => [
                        'headers' => [
                            'Urgency' => 'high'
                        ],
                        'notification' => [
                            'icon' => '/icon-192x192.png',
                            'badge' => '/badge-72x72.png'
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info('Notification Firebase envoyée', [
                    'event_id' => $this->event->id,
                    'response' => $response->json()
                ]);
            } else {
                throw new \Exception('Erreur Firebase: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Erreur envoi notification Firebase', [
                'event_id' => $this->event->id,
                'error' => $e->getMessage()
            ]);

            // Optionnel: marquer l'événement comme échoué
            $this->event->update(['status' => 'failed']);

            throw $e; // Re-lancer pour les tentatives automatiques
        }
    }

    private function getAccessToken(): string
    {
        // Utiliser le service account pour obtenir un token
        // Vous devez configurer les credentials Firebase dans votre .env
        $client = new \Google_Client();
        $client->setAuthConfig(storage_path('app/firebase-service-account.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client->fetchAccessTokenWithAssertion();

        return $token['access_token'];
    }
}
