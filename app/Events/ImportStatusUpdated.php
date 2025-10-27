<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class ImportStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $status;
    public $reportFilename;

    public function __construct($status, $reportFilename = null)
    {
        $this->status = $status;
        $this->reportFilename = $reportFilename;
    }

    public function broadcastOn()
    {
        return new Channel('import-status');
    }

    public function broadcastWith()
    {
        return [
            'status' => $this->status,
            'report' => $this->reportFilename,
        ];
    }
}
