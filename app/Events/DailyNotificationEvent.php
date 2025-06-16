<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class DailyNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function broadcastOn()
    {
        // Canal privé pour chaque stagiaire
        return new PrivateChannel('stagiaire.' . $this->user->id);
    }

    public function broadcastWith()
    {
        return [
            'message' => 'Notification quotidienne',
            'user_id' => $this->user->id,
        ];
    }
}
