<?php

namespace App\Actions\Newsletter;

use App\Enums\SubscriberStatus;
use App\Models\Subscriber;
use Illuminate\Support\Str;

class SubscribeToNewsletter
{
    /**
     * @return array{subscriber: Subscriber, should_send_confirmation: bool}
     */
    public function handle(string $email, string $locale): array
    {
        $subscriber = Subscriber::query()
            ->where('email', $email)
            ->first();

        if ($subscriber === null) {
            $subscriber = Subscriber::query()->create([
                'email' => $email,
                'status' => SubscriberStatus::Pending->value,
                'confirmation_token' => $this->generateToken(),
                'confirmed_at' => null,
                'locale' => $locale,
            ]);

            return [
                'subscriber' => $subscriber,
                'should_send_confirmation' => true,
            ];
        }

        if ($subscriber->status === SubscriberStatus::Confirmed) {
            if ($subscriber->locale !== $locale) {
                $subscriber->forceFill([
                    'locale' => $locale,
                ])->save();
            }

            return [
                'subscriber' => $subscriber,
                'should_send_confirmation' => false,
            ];
        }

        $subscriber->forceFill([
            'status' => SubscriberStatus::Pending->value,
            'confirmation_token' => $this->generateToken(),
            'confirmed_at' => null,
            'locale' => $locale,
        ])->save();

        return [
            'subscriber' => $subscriber,
            'should_send_confirmation' => true,
        ];
    }

    protected function generateToken(): string
    {
        return Str::random(64);
    }
}
