<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FreeMessageSent
{
    use Dispatchable, SerializesModels;

    public $my_user;
    public $other_user;
    /**
     * Create a new event instance.
     */
    public function __construct(int $my_user_id,int $other_user_id)
    {
        $this->my_user = User::where("id", $my_user_id)->first();
        $this->other_user = User::where("id", $other_user_id)->first();
    }

}
