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

class NewMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $chat;

    public function __construct($message, $chat) {
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
    return 'NewMessage';
  }

  public function broadcastWith(): array
{
    
    return [ 'message' => new MessageResource($this->message),'sender_id'=>$this->message->sender_id];

    //FOR SOME REASON, WE CANNOT MODIFY MessageResource
    // return [ 'message' => [
    //     'id' => $this->message->id,
    //     'text' => $this->message->message,
    //     'time' => $this->message->created_at,
    //     'sent' =>  $this->message->sender_id,
    //     'is_read' => $this->message->is_read
    //     // 'sender_name' => $message->sender->name,
    // ]];
}

// public function broadcastWhen() {
//     \Log::info(auth()->id());
//     return auth()->id() !== $this->message->sender_id;
// }
}
