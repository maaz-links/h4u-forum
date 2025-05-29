<?php

namespace App\Listeners;

use App\Events\TempBan;
use App\Mail\pastelinkmail;
use App\Services\CheckNotifPreference;
use App\Services\ModConfigValues;
use App\Services\TwilioService;
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
        //Dont send to dummy id
        if($event->user->isDummy()){
            return;
        }
        //Load Modded configs values, for queued listeners
        $modifiedConfig = ModConfigValues::LoadConfigValues();
        $subject = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailsubject.tempban');
        $message = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailmessage.tempban');

        $message = str_replace("{username}", $event->user->name, $message);
        $message = str_replace("{suspension_time}", $event->banduration, $message);
        //Mail::to($event->user->email)->send(new pastelinkmail($message, $subject));
        $event->user->notify(new \App\Notifications\GenericNotification($subject, $message));
        //Send SMS
        $shouldSMS = CheckNotifPreference::isSMSEnabled($event->user->id);
        if ($shouldSMS) {
            $twilio = new TwilioService();
            $twilio->sendSms($event->user->phone, $message);
        }
    }
}
