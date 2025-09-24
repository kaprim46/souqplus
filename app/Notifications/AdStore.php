<?php

namespace App\Notifications;

use App\Models\Advertisement;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdStore extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Advertisement $ad)
    {
        $this->ad = $ad;
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
     * @throws Exception
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->view(
                $this->getView($this->ad?->status),
                [
                    'ad' => $this->ad
                ]
            )
            ->subject($this->getSubject($this->ad?->status));
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

    /**
     * Which view to use for the email notification
     * @throws Exception
     */
    private function getView($status): string
    {
        return match ($status) {
            'pending'       => 'emails.ad.pending',
            'approved'      => 'emails.ad.approved',
            'rejected'      => 'emails.ad.rejected',
            default         => Throw new Exception('Invalid status')
        };
    }

    /**
     * Which subject to use for the email notification
     */
    private function getSubject($status): string
    {
        return match ($status) {
            'pending'       => __('Ad status pending'),
            'approved'      => __('Ad status approved'),
            'rejected'      => __('Ad status rejected'),
            default         => __('Ad status unknown')
        };
    }
}
