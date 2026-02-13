<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendTwoFactorCode extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $code)
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
        return (new MailMessage)
                    ->subject('Your Nexus Login Code')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('You are attempting to log in to Nexus.')
                    ->line('Please use the following code to complete your login:')
                    ->line(new \Illuminate\Support\HtmlString('<div style="font-size: 24px; font-weight: bold; text-align: center; padding: 10px;">' . $this->code . '</div>'))
                    ->line('This code will expire in 10 minutes.')
                    ->line('If you did not request this, please ignore this email.');
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
