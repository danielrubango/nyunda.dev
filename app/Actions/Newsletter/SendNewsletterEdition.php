<?php

namespace App\Actions\Newsletter;

use App\Enums\SubscriberStatus;
use App\Models\NewsletterEdition;
use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SendNewsletterEdition
{
    /**
     * Dispatche l'envoi de l'édition newsletter à tous les abonnés confirmés.
     * La logique d'envoi réelle est dans SendNewsletterEditionJob (un job par subscriber).
     */
    public function handle(NewsletterEdition $edition): void
    {
        if (! $edition->isDraft()) {
            throw new RuntimeException("L'édition #{$edition->id} n'est pas en statut 'draft'.");
        }

        $recipientsCount = Subscriber::query()
            ->where('status', SubscriberStatus::Confirmed->value)
            ->count();

        if ($recipientsCount === 0) {
            throw new RuntimeException('Aucun abonné confirmé.');
        }

        DB::transaction(function () use ($edition, $recipientsCount): void {
            $edition->forceFill([
                'status' => 'sending',
                'recipients_count' => $recipientsCount,
                'sent_count' => 0,
                'started_at' => now(),
            ])->save();
        });

        \App\Jobs\SendNewsletterEditionJob::dispatch($edition->id)->afterCommit();
    }
}
