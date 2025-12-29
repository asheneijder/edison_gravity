<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyMessageSummary extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $count;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $count)
    {
        $this->count = $count;
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
        return (new MailMessage)
            ->subject('Daily Swift Message Summary')
            ->line('You have ' . $this->count . ' new Swift messages today.')
            ->action('View Messages', url('/admin/swift-messages'))
            ->line('Please log in to review them.');
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
