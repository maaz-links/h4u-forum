<?php

namespace App\Listeners;

use App\Events\WarnUser;
use App\Mail\pastelinkmail;
use App\Services\CheckNotifPreference;
use App\Services\ModConfigValues;
use App\Services\TwilioService;
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
        //Dont send to dummy id
        if($event->user->isDummy()){
            return;
        }
        //Load Modded configs values, for queued listeners
        $modifiedConfig = ModConfigValues::LoadConfigValues();
        $subject = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailsubject.warning');
        $message = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailmessage.warning');

        $message = str_replace("{username}", $event->user->name, $message);
        //dd(config('mail.mailers.smtp.password'));
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
