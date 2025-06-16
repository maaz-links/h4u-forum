<?php

namespace App\Listeners;

use App\Events\CancellationRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class CancellationRequestListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CancellationRequest $event): void
    {
        if($event->user->isDummy()){
            return;
        }
        //Load Modded configs values, for queued listeners
        //$modifiedConfig = ModConfigValues::LoadConfigValues();
        // $subject = "Cancellation Request";
        // $message = nl2br("The following user has sent cancellation request.\nUserID: ".$event->user->id."\nName:".$event->user->name."\nEmail:".$event->user->email);

        //$message = str_replace("{username}", $event->user->name, $message);
        //Mail::to($event->user->email)->send(new pastelinkmail($message, $subject));
        Notification::route('mail', config('h4u.email.support_address'))->notify(new \App\Notifications\CancellationRequestNotif($event->user));
    }
}
