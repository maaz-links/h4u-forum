<?php

namespace App\Listeners;

use App\Events\PermaBan;
use App\Events\UnBanUser;
use App\Mail\pastelinkmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class UnBanUserListener implements ShouldQueue
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
    public function handle(UnBanUser $event): void
    {
        $subject = config('h4u.emailsubject.unban');
        $message = config('h4u.emailmessage.unban');
        $message = str_replace("{username}", $event->user->name, $message);
        Mail::to($event->user->email)->send(new pastelinkmail($message, $subject));
    }
}
