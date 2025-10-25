<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\NotificationService;

class SendFcmNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $title;
    public $body;
    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($user, string $title, string $body, array $data = [])
    {
        $this->user = $user;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService)
    {
        $notificationService->sendFcmToUser($this->user, $this->title, $this->body, $this->data);
    }
}
