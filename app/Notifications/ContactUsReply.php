<?php

namespace App\Notifications;

use App\Models\ContactUs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactUsReply extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ContactUs $contactUs)
    {
        $this->contactUs = $contactUs;
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
            ->view(
                "emails.reply.message",
                [
                    'msg' => $this->contactUs
                ]
            )
            ->subject(__('Contact us reply'));
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
