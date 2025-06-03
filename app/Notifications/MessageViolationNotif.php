<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageViolationNotif extends Notification implements ShouldQueue
{
    use Queueable;

    protected $alert;

    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->salutation('')
            ->subject("Potential Violation in Messages")
            ->line("The following message potentially violates rules and must be checked.")
            //->line("User ID: ". $this->alert->user->name)
            ->line("Username: ". $this->alert->user->name)
            ->line("User Email: ". $this->alert->user->email)
            ->line("Message body: ")
            ->line($this->alert->message_body);
            //->action('View Chat', url('/chat'));
    }
}