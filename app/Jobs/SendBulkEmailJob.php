<?php

namespace App\Jobs;

use App\Mail\BulkNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;
    protected $subject;
    protected $message;
    protected $senderId;

    /**
     * Create a new job instance.
     */
    public function __construct($users, $subject, $message, $senderId)
    {
        $this->users = $users;
        $this->subject = $subject;
        $this->message = $message;
        $this->senderId = $senderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $batchSize = 50; // Send 50 emails at a time
        $chunks = $this->users->chunk($batchSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $user) {
                try {
                    Mail::to($user->email)->send(
                        new BulkNotificationMail(
                            $this->subject,
                            $this->message,
                            $user->name
                        )
                    );

                    Log::info("Email sent to {$user->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
                }
            }

            // Small delay between batches to avoid rate limiting
            sleep(1);
        }

        // Update notification history status
        \DB::table('notification_history')
            ->where('created_by', $this->senderId)
            ->where('type', 'email')
            ->latest()
            ->limit(1)
            ->update(['status' => 'sent']);
    }
}
