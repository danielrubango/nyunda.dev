<?php

namespace App\Actions\Newsletter;

use App\Enums\SubscriberStatus;
use App\Models\Subscriber;

class ConfirmNewsletterSubscription
{
    public function handle(string $token): bool
    {
        $subscriber = Subscriber::query()
            ->where('confirmation_token', $token)
            ->first();

        if ($subscriber === null) {
            return false;
        }

        if ($subscriber->status === SubscriberStatus::Confirmed) {
            return true;
        }

        if ($subscriber->status !== SubscriberStatus::Pending) {
            return false;
        }

        $subscriber->forceFill([
            'status' => SubscriberStatus::Confirmed->value,
            'confirmed_at' => now(),
        ])->save();

        return true;
    }
}
