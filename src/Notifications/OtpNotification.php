<?php

namespace BenBjurstrom\Otpz\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class OtpNotification extends Notification
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
        $url = URL::temporarySignedRoute('otplink', now()->addMinutes(5), [
            'code' => $this->code,
            'email' => $notifiable->email,
        ]);

        $code = substr_replace($this->code, '-', 3, 0);
        $code = substr_replace($code, '-', 7, 0);

        return (new MailMessage)
            ->subject('Your '.config('app.name').' login code')
            ->greeting('Use the following code to login to your account:')
            ->line($code)
            ->action('Open In Browser', $url)
            ->line('Note: this code expires after 5 minutes.');
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
