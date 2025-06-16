<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageToFakeUserNotif extends Notification implements ShouldQueue
{
    use Queueable;

    protected $dummyUser;
    protected $message;

    public function __construct($message,$dummyUser)
    {
        $this->message = $message;
        $this->dummyUser = $dummyUser;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->salutation('')
            ->subject("Message to Fake User")
            ->line("The following message is sent to fake user in chat.")
            //->line("User ID: ". $this->alert->user->name)
            ->line("Sender: ". $this->message->sender->name)
            ->line("Recipient: ". $this->dummyUser->name)
            ->line("Message body: ")
            ->line($this->message->message);
            //->action('View Chat', url('/chat'));
    }
}