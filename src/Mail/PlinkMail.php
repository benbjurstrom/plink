<?php

namespace BenBjurstrom\Plink\Mail;

use BenBjurstrom\Plink\Models\Plink;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PlinkMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected Plink $plink)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Secure '.config('app.name').' Login Link',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $expiration = now()->addMinutes(config('plink.expiration', 5));
        $url = URL::temporarySignedRoute('plink.show', $expiration, [
            'id' => $this->plink->id,
            'session' => request()->session()->getId(),
        ]);

        $template = config('plink.template', 'plink::mail.plink');

        return new Content(
            markdown: $template,
            with: [
                'url' => $url,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
