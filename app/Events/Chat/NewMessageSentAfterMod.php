<?php

namespace App\Events\Chat;

use App\Http\Resources\MessageResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class NewMessageSentAfterMod implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $queue;
    public $message;
    public $chat;

    public function __construct($message, $chat) {
        $this->queue = env('CHAT_MESSAGES_QUEUE_NAME', 'default');
        $this->message = $message;
        $this->chat = $chat;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn() {
        return [
            new PrivateChannel('chatter.' . $this->chat->id),  
            new PrivateChannel('App.Models.User.' . $this->chat->user1_id) ,
            new PrivateChannel('App.Models.User.' . $this->chat->user2_id)  
        ];
    }

  public function broadcastAs() {
    return 'NewMessageAfterMod';
  }

}
