<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $queue;
    public $message;

    public function __construct($message)
    {
        $this->queue = env('CHAT_MESSAGES_QUEUE_NAME', 'default');
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // return new Channel('chat');
        return new PrivateChannel('chat');
        
    }
    public function broadcastAs()
    {
        return 'MessageSent'; // Explicitly set broadcast event name
    }

// public function broadcastWith()
// {
//     return ['message' => $this->message]; // Ensure consistent data structure
// }
}