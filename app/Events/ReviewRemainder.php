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

class ReviewRemainder
{
    use Dispatchable, SerializesModels;

    public $my_user;
    public $other_user;
    /**
     * Create a new event instance.
     */
    public function __construct($my_user,$other_user)
    {
        $this->my_user = $my_user;
        $this->other_user = $other_user;
    }

}
