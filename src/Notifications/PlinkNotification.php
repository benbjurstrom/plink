<?php

namespace BenBjurstrom\Plink\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class PlinkNotification extends Notification
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
        $url = URL::temporarySignedRoute('plink.show', now()->addMinutes(5), [
            'code' => $this->code,
            'id' => $notifiable->id,
        ]);

        // Format the code with hyphens for readability
        $formattedCode = substr_replace($this->code, '-', 3, 0);
        $formattedCode = substr_replace($formattedCode, '-', 7, 0);

        return (new MailMessage)
            ->subject('Your '.config('app.name').' Login Link')
            ->line('Click the button below to securely log in to your account:')
            ->action('Sign-In to '.config('app.name'), $url)
            ->line('If you cannot access this link from the device you\'re authorizing, you may manually enter the code below:')
            ->line($formattedCode)
            ->line('**Important:** Only enter this code after verifying that the URL in your browser is on the correct domain:'.config('app.url'))
            ->line('This code expires after 5 minutes and can only be used once.')
            ->salutation('Thank you for using '.config('app.name').'!');
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
