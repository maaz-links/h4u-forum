<?php

namespace App\Events;

use App\Models\FakeProfileSetting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GenerateFakeProfiles
{
    use Dispatchable, SerializesModels;

    public $given_data;
    public $script;
    /**
     * Create a new event instance.
     */
    public function __construct($validated_data,FakeProfileSetting $script){

        $this->given_data = $validated_data;
        $this->script = $script;
    }

}
