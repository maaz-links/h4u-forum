<?php

namespace App\Listeners;

use App\Events\ChatUnlocked;
use App\Events\PermaBan;
use App\Events\ReviewRecieved;
use App\Mail\pastelinkmail;
use App\Models\User;
use App\Services\CheckNotifPreference;
use App\Services\ModConfigValues;
use App\Services\TwilioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ReviewRecievedListener implements ShouldQueue
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
    public function handle(ReviewRecieved $event): void
    {
        //dd('hlo');
        $reviewerUser = $event->review->reviewer;
        $reviewedUser = $event->review->reviewedUser;
        //Dont send to dummy id
        
        //In this case, notification is only sent to HOSTESS role
        if($reviewedUser->isDummy() || ($reviewedUser->role != User::ROLE_HOSTESS)){
            return;
        }
        $modifiedConfig = ModConfigValues::LoadConfigValues();
        $subject = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailsubject.review_recieved');
        $message = ModConfigValues::getModifiedConfig($modifiedConfig,'h4u.emailmessage.review_recieved');
        $message = str_replace("{username}", $reviewedUser->name, $message);
        $message = str_replace("{other_username}", $reviewerUser->name, $message);
        $message = str_replace("{rating}", $event->review->rating, $message);
        //Mail::to($reviewedUser->email)->send(new pastelinkmail($message, $subject));
        $reviewedUser->notify(new \App\Notifications\GenericNotification($subject, $message));
        //Send SMS
        $shouldSMS = CheckNotifPreference::isSMSEnabled($reviewedUser->id);
        if ($shouldSMS) {
            $twilio = new TwilioService();
            $twilio->sendSms($reviewedUser->phone, $message);
        }
    }
}
