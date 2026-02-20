<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterDoubleOptInMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $locale,
        public string $confirmUrl,
        public string $unsubscribeUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->locale === 'en'
                ? 'Confirm your newsletter subscription'
                : 'Confirmez votre inscription a la newsletter',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.double-opt-in',
            with: [
                'locale' => $this->locale,
                'confirmUrl' => $this->confirmUrl,
                'unsubscribeUrl' => $this->unsubscribeUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
