<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\TestNotification;
use App\Models\Stagiaire;

class NotificationService
{
    public function notifyQuizAvailable(string $quizTitle, int $quizId): void
    {
        // Récupérer le quiz avec sa formation
        $quiz = \App\Models\Quiz::with('formation')->find($quizId);

        if (!$quiz || !$quiz->formation) {
            return;
        }

        // Récupérer tous les stagiaires qui ont une catalogue de formation liée à cette formation
        $stagiaires = Stagiaire::whereHas('catalogue_formations', function ($query) use ($quiz) {
            $query->where('formation_id', $quiz->formation->id);
        })->with('user')->get();

        foreach ($stagiaires as $stagiaire) {
            if ($stagiaire->user) {
                Notification::create([
                    'user_id' => $stagiaire->user->id,
                    'type' => 'quiz',
                    'message' => "Un nouveau quiz \"{$quizTitle}\" est disponible !",
                    'data' => [
                        'quiz_id' => $quizId,
                        'quiz_title' => $quizTitle
                    ],
                    'read' => false
                ]);

                event(new TestNotification([
                    'type' => 'quiz',
                    'message' => "Un nouveau quiz \"{$quizTitle}\" est disponible !",
                    'quiz_title' => $quizTitle,
                ]));
            }
        }
    }

    public function notifyQuizCompleted(int $userId, int $quizId, int $score, int $totalQuestions): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => 'quiz',
            'message' => "Vous avez obtenu {$score}/{$totalQuestions} points au quiz !",
            'data' => [
                // 'quiz_id' => $quizId,
                'score' => $score,
                'total_questions' => $totalQuestions
            ],
            'read' => false
        ]);
    }

    public function notifyRewardEarned(int $userId, int $points, ?string $rewardType = null): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => 'badge',
            'message' => "Vous avez gagné {$points} points" . ($rewardType ? " et un {$rewardType}" : "") . " !",
            'data' => [
                'points' => $points,
                'reward_type' => $rewardType
            ],
            'read' => false
        ]);
    }

    public function notifyFormationUpdate(int $userId, string $formationTitle, int $formationId, ?string $dateDebut = null): void
    {
        $message = $dateDebut
            ? "Votre formation \"{$formationTitle}\" commence le {$dateDebut} !"
            : "La formation \"{$formationTitle}\" a été mise à jour !";
        Notification::create([
            'user_id' => $userId,
            'type' => 'formation',
            'message' => $message,
            'data' => [
                'formation_id' => $formationId,
                'formation_title' => $formationTitle,
                'date_debut' => $dateDebut
            ],
            'read' => false
        ]);
        // Optionnel : broadcast Pusher
        event(new \App\Events\TestNotification([
            'type' => 'formation',
            'message' => $message,
            // 'formation_id' => $formationId,
            'formation_title' => $formationTitle,
            'date_debut' => $dateDebut,
            // 'user_id' => $userId
        ]));
    }

    public function notifyMediaCreated(int $userId, string $mediaTitle, int $mediaId): void
    {
        $message = "Un nouveau média \"{$mediaTitle}\" a été ajouté !";
        Notification::create([
            'user_id' => $userId,
            'type' => 'media',
            'title' => 'un nouveau média a été ajouté',
            'message' => $message,
            'data' => [
                'media_id' => $mediaId,
                'media_title' => $mediaTitle
            ],
            'read' => false
        ]);
        // Broadcast temps réel Pusher
        event(new \App\Events\TestNotification([
            'type' => 'media',
            'message' => $message,
            // 'media_id' => $mediaId,
            'media_title' => $mediaTitle,
            // 'user_id' => $userId
        ]));
    }

    public function notifyCustom(int $userId, string $type, string $message): void
    {
        \App\Models\Notification::create([
            'user_id' => $userId,
            'title' => 'Nouvelle notification',
            'type' => $type,
            'message' => $message,
            'data' => [],
            'read' => false
        ]);
        // Optionnel : broadcast Pusher
        event(new \App\Events\TestNotification([
            'type' => $type,
            'message' => $message,
            'user_id' => $userId
        ]));
    }

    /**
     * Envoie une notification FCM à un utilisateur (si fcm_token présent)
     */
    public function sendFcmToUser($user, $title, $body, $data = [])
    {
        if (!$user || !$user->fcm_token) {
            return false;
        }
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

            // Convertir toutes les valeurs du data en string pour FCM
            $data = array_map('strval', $data);

            // Build FCM HTTP v1 payload with platform-specific options
            $appUrl = env('APP_URL', '/');
            $payload = [
                'message' => [
                    'token' => $user->fcm_token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    // data payload (all values already converted to string)
                    'data' => $data,
                    // Android specific options - helps Android display & click handling
                    'android' => [
                        'notification' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'icon' => '/logo.png',
                        ],
                    ],
                    // Web push specific options - ensures browser service worker receives notification
                    'webpush' => [
                        'headers' => [
                            'TTL' => '86400'
                        ],
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                            'icon' => '/logo.png',
                        ],
                        'fcm_options' => [
                            'link' => $appUrl,
                        ],
                    ],
                    // APNs (iOS) options - basic sound/badge
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
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
                'json' => $payload,
            ]);

            if ($response->getStatusCode() === 200) {
                // Succès, notification envoyée
                $result = json_decode($response->getBody(), true);
                \Log::info('FCM envoyé avec succès', $result);
                return true;
            } else {
                // Erreur, notification non envoyée
                \Log::error('Erreur FCM', [
                    'status' => $response->getStatusCode(),
                    'body' => (string) $response->getBody()
                ]);
                // dd('KO+ FCM', $response->getStatusCode(), (string) $response->getBody());
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Erreur envoi FCM: ' . $e->getMessage());
            // dd('KO FCM', $e->getMessage());
            return false;
        }
    }
}
