<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ForgotPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected User $user)
    {
        /**
         * Generate a random 6 digit number and save it to the user's forget_password_code column.
         */
        while (true) {
            $forgetPasswordCode = rand(100000, 999999);
            if (! User::where('forget_password_code', $forgetPasswordCode)->exists()) {
                $this->user->forget_password_code = $forgetPasswordCode;
                $this->user->save();
                break;
            }
        }
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
                'emails.customer.forget_password',
                [
                    'user' => $this->user,
                    'otp'  => $this->user->forget_password_code,
                ]
            )
            ->subject(__('Reset password request'));
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