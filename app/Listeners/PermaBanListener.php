<?php

namespace App\Listeners;

use App\Events\PermaBan;
use App\Mail\pastelinkmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class PermaBanListener implements ShouldQueue
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
    public function handle(PermaBan $event): void
    {
        $subject = config('h4u.emailsubject.permaban');
        $message = config('h4u.emailmessage.permaban');
        $message = str_replace("{username}", $event->user->name, $message);
        Mail::to($event->user->email)->send(new pastelinkmail($message, $subject));
    }
}
