<?php

namespace BenBjurstrom\Plink\Notifications;

use BenBjurstrom\Plink\Models\Plink;
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
    public function __construct(public Plink $plink)
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
            'id' => $this->plink->id,
            'session' => request()->session()->getId(),
        ]);

        return (new MailMessage)
            ->subject('Your '.config('app.name').' Login Link')
            ->line('Click the button below to securely log in to your account:')
            ->action('Sign-In to '.config('app.name'), $url)
            ->line('This link expires after 5 minutes and can only be used once.')
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
