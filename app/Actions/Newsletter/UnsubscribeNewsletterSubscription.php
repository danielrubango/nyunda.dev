<?php

namespace App\Actions\Newsletter;

use App\Enums\SubscriberStatus;
use App\Models\Subscriber;

class UnsubscribeNewsletterSubscription
{
    public function handle(string $token): bool
    {
        $subscriber = Subscriber::query()
            ->where('confirmation_token', $token)
            ->first();

        if ($subscriber === null) {
            return false;
        }

        if ($subscriber->status === SubscriberStatus::Unsubscribed) {
            return true;
        }

        $subscriber->forceFill([
            'status' => SubscriberStatus::Unsubscribed->value,
            'confirmed_at' => null,
        ])->save();

        return true;
    }
}
