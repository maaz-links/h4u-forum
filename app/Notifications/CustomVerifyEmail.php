<?php

namespace App\Notifications;

use Carbon\Carbon;
use Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use URL;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail($notifiable)
    // {
    //     $verificationUrl = $this->verificationUrl($notifiable);

    //     return (new MailMessage)
    //         ->subject('Verify Email Address')
    //         ->line('Please click the button below to verify your email address.')
    //         ->action('Verify Email Address', $verificationUrl)
    //         ->line('If you did not create an account, no further action is required.');
    // }
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        
        // Parse the signed URL to get the parameters
        $parsedUrl = parse_url($verificationUrl);
        parse_str($parsedUrl['query'], $queryParams);


        $path = $parsedUrl['path'];  // Contains the ID in the path
        // Extract the ID from the path (e.g., "/api/email/verify/8")
        $pathParts = explode('/', $path);
        $id = end($pathParts);

        //dd($queryParams);
        // Create a frontend URL with the same parameters
        $frontendUrl = env('FRONTEND_URL').'/verify-email?' . http_build_query([
            'expires' => $queryParams['expires'],
            'hash' => $queryParams['hash'],
            'id' => $id,//$queryParams['id'],
            'signature' => $queryParams['signature']
        ]);

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $frontendUrl);
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(30),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
