<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;
    protected $message;
    protected $senderId;

    /**
     * Create a new job instance.
     */
    public function __construct($users, $message, $senderId)
    {
        $this->users = $users;
        $this->message = $message;
        $this->senderId = $senderId;
    }

    /**
     * Execute the job - Send push notifications via Firebase Cloud Messaging
     */
    public function handle(): void
    {
        $fcmServerKey = env('FIREBASE_SERVER_KEY');

        if (!$fcmServerKey) {
            Log::error('Firebase server key not configured');
            return;
        }

        $fcmTokens = $this->users->pluck('fcm_token')->filter()->toArray();

        if (empty($fcmTokens)) {
            Log::warning('No FCM tokens found for users');
            return;
        }

        // Send in batches of 100 (FCM limit is 1000)
        $chunks = array_chunk($fcmTokens, 100);

        foreach ($chunks as $tokensBatch) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $fcmServerKey,
                    'Content-Type' => 'application/json',
                ])->post('https://fcm.googleapis.com/fcm/send', [
                    'registration_ids' => $tokensBatch,
                    'notification' => [
                        'title' => 'Wizi Learn',
                        'body' => $this->message,
                        'icon' => 'ic_notification',
                        'color' => '#FF6B35',
                        'sound' => 'default',
                    ],
                    'data' => [
                        'type' => 'admin_message',
                        'message' => $this->message,
                        'timestamp' => now()->toISOString(),
                    ],
                    'priority' => 'high',
                ]);

                if ($response->successful()) {
                    Log::info("Push notification sent to " . count($tokensBatch) . " devices");
                } else {
                    Log::error("Failed to send push notification: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Error sending push notification: " . $e->getMessage());
            }

            // Small delay between batches
            sleep(1);
        }

        // Update notification history status
        \DB::table('notification_history')
            ->where('created_by', $this->senderId)
            ->where('type', 'push')
            ->latest()
            ->limit(1)
            ->update(['status' => 'sent']);
    }
}
