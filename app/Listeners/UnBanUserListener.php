<?php

namespace App\Listeners;

use App\Events\PermaBan;
use App\Events\UnBanUser;
use App\Mail\pastelinkmail;
use App\Services\CheckNotifPreference;
use App\Services\ModConfigValues;
use App\Services\TwilioService;
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
        //Load Modded configs values, for queued listeners
        $modifiedConfig = ModConfigValues::LoadConfigValues();
        $subject = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailsubject.unban');
        $message = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailmessage.unban');

        $message = str_replace("{username}", $event->user->name, $message);
        Mail::to($event->user->email)->send(new pastelinkmail($message, $subject));

        //Send SMS
        $shouldSMS = CheckNotifPreference::isSMSEnabled($event->user->id);
        if ($shouldSMS) {
            $twilio = new TwilioService();
            $twilio->sendSms($event->user->phone, $message);
        }
    }
}
