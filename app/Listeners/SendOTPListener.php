<?php

namespace App\Listeners;

use App\Events\SendOTP;
use App\Events\SendSMS;
use App\Mail\pastelinkmail;
use App\Services\CheckNotifPreference;
use App\Services\ModConfigValues;
use App\Services\TwilioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use LoadConfigValues;
use Twilio\Exceptions\TwilioException;

class SendOTPListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SendOTP $event): void
    {

        //Dont send to dummy id
        if($event->user->isDummy()){
            return;
        }
        //Load Modded configs values, for queued listeners
        $modifiedConfig = ModConfigValues::LoadConfigValues();
        $subject = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailsubject.otp');
        $message = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailmessage.otp');

        $message = str_replace("{otp}", $event->otp, $message);
        // Mail::to($event->user->email)->send(new pastelinkmail($message, $subject));

        //Send SMS
        // $shouldSMS = CheckNotifPreference::isSMSEnabled($event->user->id);
        $shouldSMS = true;
        if ($shouldSMS) {
            $twilio = new TwilioService();
            $twilio->sendSms($event->user->phone, $message);
        }
    
    }

  
}