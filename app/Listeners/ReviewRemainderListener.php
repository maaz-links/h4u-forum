<?php

namespace App\Listeners;

use App\Events\ChatUnlocked;
use App\Events\PermaBan;
use App\Events\ReviewRecieved;
use App\Events\ReviewRemainder;
use App\Mail\pastelinkmail;
use App\Models\User;
use App\Services\CheckNotifPreference;
use App\Services\ModConfigValues;
use App\Services\TwilioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ReviewRemainderListener implements ShouldQueue
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
    public function handle(ReviewRemainder $event): void
    {
        if($event->other_user->isDummy()){
            return;
        }
        $modifiedConfig = ModConfigValues::LoadConfigValues();
        $subject = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailsubject.review_remainder');
        $message = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailmessage.review_remainder');
        $delay = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.reviews.review_delay');
        
        $message = str_replace("{username}", $event->other_user->name, $message);
        $message = str_replace("{other_username}", $event->my_user->name, $message);
        $message = str_replace("{delay}", $delay, $message);
        
        $event->other_user->notify(new \App\Notifications\GenericNotification($subject, $message));
        //Send SMS
        $shouldSMS = CheckNotifPreference::isSMSEnabled($event->other_user->id);
        if ($shouldSMS) {
            $twilio = new TwilioService();
            $twilio->sendSms($event->other_user->phone, $message);
        }
    }
}
