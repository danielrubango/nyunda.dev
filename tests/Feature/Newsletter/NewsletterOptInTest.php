<?php

use App\Enums\SubscriberStatus;
use App\Jobs\SendDoubleOptInEmailJob;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

test('guest can subscribe and receives pending status with confirmation job', function () {
    Queue::fake();

    $response = $this->post(route('newsletter.subscriptions.store'), [
        'email' => 'new-subscriber@example.com',
        'locale' => 'fr',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');

    $subscriber = Subscriber::query()
        ->where('email', 'new-subscriber@example.com')
        ->first();

    expect($subscriber)->not->toBeNull()
        ->and($subscriber->status)->toBe(SubscriberStatus::Pending)
        ->and($subscriber->confirmation_token)->not->toBeNull()
        ->and($subscriber->locale)->toBe('fr');

    Queue::assertPushed(SendDoubleOptInEmailJob::class, 1);
});

test('already confirmed subscriber does not receive a new confirmation job', function () {
    Queue::fake();
    Subscriber::factory()->confirmed()->create([
        'email' => 'confirmed@example.com',
        'confirmation_token' => Str::random(64),
    ]);

    $response = $this->post(route('newsletter.subscriptions.store'), [
        'email' => 'confirmed@example.com',
        'locale' => 'en',
    ]);

    $response->assertRedirect();

    Queue::assertNotPushed(SendDoubleOptInEmailJob::class);
    $this->assertDatabaseHas('subscribers', [
        'email' => 'confirmed@example.com',
        'status' => SubscriberStatus::Confirmed->value,
        'locale' => 'en',
    ]);
});

test('confirmation route confirms pending subscriber', function () {
    $subscriber = Subscriber::factory()->create([
        'status' => SubscriberStatus::Pending->value,
        'confirmation_token' => Str::random(64),
        'confirmed_at' => null,
    ]);

    $response = $this->get(route('newsletter.confirm', [
        'token' => $subscriber->confirmation_token,
    ]));

    $response->assertRedirect(route('blog.index'));
    $response->assertSessionHas('status');

    $subscriber->refresh();

    expect($subscriber->status)->toBe(SubscriberStatus::Confirmed)
        ->and($subscriber->confirmed_at)->not->toBeNull();
});

test('unsubscribe route marks subscriber as unsubscribed', function () {
    $subscriber = Subscriber::factory()->confirmed()->create([
        'confirmation_token' => Str::random(64),
    ]);

    $response = $this->get(route('newsletter.unsubscribe', [
        'token' => $subscriber->confirmation_token,
    ]));

    $response->assertRedirect(route('blog.index'));
    $response->assertSessionHas('status');

    $subscriber->refresh();

    expect($subscriber->status)->toBe(SubscriberStatus::Unsubscribed)
        ->and($subscriber->confirmed_at)->toBeNull();
});

test('newsletter subscription payload is validated', function () {
    Queue::fake();

    $response = $this->post(route('newsletter.subscriptions.store'), [
        'email' => 'invalid',
        'locale' => 'de',
    ]);

    $response->assertSessionHasErrors(['email', 'locale']);
    Queue::assertNotPushed(SendDoubleOptInEmailJob::class);
});
