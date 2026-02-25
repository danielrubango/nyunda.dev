<?php

namespace App\Jobs;

use App\Enums\SubscriberStatus;
use App\Mail\NewsletterEditionMail;
use App\Models\NewsletterEdition;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNewsletterEditionJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 3600;

    public function __construct(
        public int $editionId,
    ) {}

    public function handle(): void
    {
        $edition = NewsletterEdition::query()->find($this->editionId);

        if ($edition === null || $edition->status !== 'sending') {
            return;
        }

        $contentItems = $edition->contentItems();

        $sentCount = 0;

        Subscriber::query()
            ->where('status', SubscriberStatus::Confirmed->value)
            ->orderBy('id')
            ->chunk(100, function ($subscribers) use ($edition, $contentItems, &$sentCount): void {
                foreach ($subscribers as $subscriber) {
                    try {
                        Mail::to($subscriber->email)->send(
                            new NewsletterEditionMail(
                                edition: $edition,
                                locale: $subscriber->locale,
                                contentItems: $contentItems,
                                unsubscribeUrl: route('newsletter.unsubscribe', [
                                    'token' => $subscriber->confirmation_token ?? '',
                                ]),
                            ),
                        );
                        $sentCount++;
                    } catch (Throwable) {
                        // On continue même si un envoi échoue
                    }
                }

                // Mise à jour du compteur après chaque chunk
                DB::table('newsletter_editions')
                    ->where('id', $edition->id)
                    ->update(['sent_count' => $sentCount]);
            });

        $edition->forceFill([
            'status' => 'sent',
            'sent_count' => $sentCount,
            'completed_at' => now(),
        ])->save();
    }

    public function failed(Throwable $exception): void
    {
        NewsletterEdition::query()
            ->where('id', $this->editionId)
            ->update(['status' => 'failed']);
    }
}
