<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeSetPassword extends Notification
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
    public function toMail(object $notifiable): MailMessage
    {
        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'setup.account',
            now()->addHours(24),
            ['id' => $notifiable->id, 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        return (new MailMessage)
            ->subject('Security Notification - ' . config('app.name'))
            ->greeting('Hello!')
            ->line('A request has been received to access or setup your account.')
            ->line('Please click the button below to securely set your password and configure Multi-Factor Authentication (MFA).')
            ->action('Secure Account Setup', $url)
            ->line('This link will expire in 24 hours.')
            ->salutation(config('app.name'));
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
