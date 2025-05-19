<?php

namespace App\Listeners;

use App\Events\ChatUnlocked;
use App\Events\PermaBan;
use App\Mail\pastelinkmail;
use App\Services\CheckNotifPreference;
use App\Services\ModConfigValues;
use App\Services\TwilioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ChatUnlockedListener implements ShouldQueue
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
    public function handle(ChatUnlocked $event): void
    {
        $modifiedConfig = ModConfigValues::LoadConfigValues();
        $subject = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailsubject.unlockchat');
        $message = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailmessage.unlockchat');
        $message = str_replace("{username}", $event->other_user->name, $message);
        $message = str_replace("{other_username}", $event->my_user->name, $message);
        Mail::to($event->other_user->email)->send(new pastelinkmail($message, $subject));

        //Send SMS
        $shouldSMS = CheckNotifPreference::isSMSEnabled($event->other_user->id);
        if ($shouldSMS) {
            $twilio = new TwilioService();
            $twilio->sendSms($event->other_user->phone, $message);
        }
    }
}
