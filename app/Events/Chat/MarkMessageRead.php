<?php

namespace App\Events\Chat;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MarkMessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $queue;
    public $messageId;
    public $readerId;
    public $chatId;

    public function __construct($messageId, $readerId, $chatId)
    {
        $this->queue = env('CHAT_MESSAGES_QUEUE_NAME', 'default');
        $this->messageId = $messageId;
        $this->readerId = $readerId;
        $this->chatId = $chatId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chatter.'.$this->chatId);
    }

    public function broadcastAs()
    {
        return 'MessageRead';
    }
}
