<?php

namespace App\Listeners;

use App\Events\WarnUser;
use App\Mail\pastelinkmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class WarnUserListener implements ShouldQueue
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
    public function handle(WarnUser $event): void
    {
        $subject = config('h4u.emailsubject.warning');
        $message = config('h4u.emailmessage.warning');
        $message = str_replace("{username}", $event->user->name, $message);
        //dd(config('mail.mailers.smtp.password'));
        Mail::to($event->user->email)->send(new pastelinkmail($message, $subject));
    }
}
