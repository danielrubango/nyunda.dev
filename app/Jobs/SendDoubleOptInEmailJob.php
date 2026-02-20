<?php

namespace App\Jobs;

use App\Mail\NewsletterDoubleOptInMail;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendDoubleOptInEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $subscriberId,
    ) {}

    public function handle(): void
    {
        $subscriber = Subscriber::query()->find($this->subscriberId);

        if ($subscriber === null || $subscriber->confirmation_token === null) {
            return;
        }

        $confirmUrl = route('newsletter.confirm', [
            'token' => $subscriber->confirmation_token,
        ]);
        $unsubscribeUrl = route('newsletter.unsubscribe', [
            'token' => $subscriber->confirmation_token,
        ]);

        Mail::to($subscriber->email)->send(
            new NewsletterDoubleOptInMail(
                locale: $subscriber->locale,
                confirmUrl: $confirmUrl,
                unsubscribeUrl: $unsubscribeUrl,
            ),
        );
    }
}
