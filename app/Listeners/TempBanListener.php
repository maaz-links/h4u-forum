<?php

namespace App\Listeners;

use App\Events\TempBan;
use App\Mail\pastelinkmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;

class TempBanListener implements ShouldQueue
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
    public function handle(TempBan $event): void
    {
        $subject = config('h4u.emailsubject.tempban');
        $message = config('h4u.emailmessage.tempban');
        $message = str_replace("{username}", $event->user->name, $message);
        $message = str_replace("{suspension_time}", $event->banduration, $message);
        Mail::to($event->user->email)->send(new pastelinkmail($message, $subject));
    }
}
