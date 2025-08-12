<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $title;
    public $body;

    public function __construct($userId, $title, $body)
    {
        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
    }

    public function broadcastOn()
    {
        return new Channel('user-' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
}
