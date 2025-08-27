<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;

    public function __construct($message, $userId)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('chat.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    // jo data frontend ko bhejna hai
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'userId'  => $this->userId,
        ];
    }
}
