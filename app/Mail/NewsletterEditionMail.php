<?php

namespace App\Mail;

use App\Models\ContentItem;
use App\Models\NewsletterEdition;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterEditionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, ContentItem>  $contentItems
     */
    public function __construct(
        public readonly NewsletterEdition $edition,
        public readonly string $locale,
        public readonly Collection $contentItems,
        public readonly string $unsubscribeUrl,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->locale === 'en'
            ? $this->edition->subject_en
            : $this->edition->subject_fr;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $intro = $this->locale === 'en'
            ? $this->edition->intro_en
            : $this->edition->intro_fr;

        $rows = $this->contentItems->map(function (ContentItem $item): ?array {
            $translation = $item->translations
                ->firstWhere('locale', $this->locale)
                ?? $item->translations->firstWhere('locale', 'fr')
                ?? $item->translations->first();

            if ($translation === null) {
                return null;
            }

            return [
                'content_item' => $item,
                'translation' => $translation,
            ];
        })->filter()->values();

        return new Content(
            view: 'emails.newsletter.edition',
            with: [
                'locale' => $this->locale,
                'intro' => $intro,
                'rows' => $rows,
                'unsubscribeUrl' => $this->unsubscribeUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
