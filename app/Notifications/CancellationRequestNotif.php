<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancellationRequestNotif extends Notification
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->salutation('')
            ->subject("Cancellation Request")
            ->line("The following user has sent cancellation request.")
            ->line("User ID: ". $this->user->id)
            ->line("Username: ". $this->user->name)
            ->line("User Email: ". $this->user->email);
            //->action('View Chat', url('/chat'));
    }
}