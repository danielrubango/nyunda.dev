<?php

use App\Actions\Content\TrackContentRead;
use App\Enums\ContentStatus;
use App\Models\ContentItem;
use App\Models\ContentRead;
use App\Models\ContentTranslation;
use App\Models\User;
use Illuminate\Support\Carbon;

test('guest first visit on internal article increments reads and queues visitor cookie', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'reads_count' => 0,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'tracking-guest-first-visit',
        'title' => 'Tracking guest first visit',
    ]);

    $response = $this->get('/blog/fr/tracking-guest-first-visit');

    $response->assertSuccessful();
    $response->assertCookie(TrackContentRead::VISITOR_COOKIE_NAME);
    expect(ContentRead::query()->where('content_item_id', $contentItem->id)->count())->toBe(1);
    expect((int) $contentItem->refresh()->reads_count)->toBe(1);
});

test('guest visits are deduplicated for four hours and counted again after window', function () {
    $fixedNow = Carbon::parse('2026-02-21 10:00:00');
    Carbon::setTestNow($fixedNow);

    try {
        $contentItem = ContentItem::factory()->published()->internalPost()->create([
            'reads_count' => 0,
        ]);

        ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
            'slug' => 'tracking-guest-window',
            'title' => 'Tracking guest window',
        ]);

        $this->withCookie(TrackContentRead::VISITOR_COOKIE_NAME, 'guest-window-id')
            ->get('/blog/fr/tracking-guest-window')
            ->assertSuccessful();

        expect((int) $contentItem->refresh()->reads_count)->toBe(1);

        Carbon::setTestNow($fixedNow->copy()->addHours(2));

        $this->withCookie(TrackContentRead::VISITOR_COOKIE_NAME, 'guest-window-id')
            ->get('/blog/fr/tracking-guest-window')
            ->assertSuccessful();

        expect((int) $contentItem->refresh()->reads_count)->toBe(1);

        Carbon::setTestNow($fixedNow->copy()->addHours(4));

        $this->withCookie(TrackContentRead::VISITOR_COOKIE_NAME, 'guest-window-id')
            ->get('/blog/fr/tracking-guest-window')
            ->assertSuccessful();

        expect((int) $contentItem->refresh()->reads_count)->toBe(2);
    } finally {
        Carbon::setTestNow();
    }
});

test('authenticated user visits are deduplicated for one hour and counted again after window', function () {
    $fixedNow = Carbon::parse('2026-02-21 12:00:00');
    Carbon::setTestNow($fixedNow);

    try {
        $user = User::factory()->create();
        $contentItem = ContentItem::factory()->published()->internalPost()->create([
            'reads_count' => 0,
        ]);

        ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
            'slug' => 'tracking-auth-window',
            'title' => 'Tracking auth window',
        ]);

        $this->actingAs($user)
            ->get('/blog/fr/tracking-auth-window')
            ->assertSuccessful();

        expect((int) $contentItem->refresh()->reads_count)->toBe(1);

        Carbon::setTestNow($fixedNow->copy()->addMinutes(30));

        $this->actingAs($user)
            ->get('/blog/fr/tracking-auth-window')
            ->assertSuccessful();

        expect((int) $contentItem->refresh()->reads_count)->toBe(1);

        Carbon::setTestNow($fixedNow->copy()->addHour());

        $this->actingAs($user)
            ->get('/blog/fr/tracking-auth-window')
            ->assertSuccessful();

        expect((int) $contentItem->refresh()->reads_count)->toBe(2);
    } finally {
        Carbon::setTestNow();
    }
});

test('external article route does not track reads', function () {
    $contentItem = ContentItem::factory()->published()->externalPost()->create([
        'reads_count' => 0,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'tracking-external',
        'title' => 'Tracking external',
        'external_url' => 'https://example.com/external-read',
    ]);

    $this->get('/blog/fr/tracking-external')
        ->assertRedirect('https://example.com/external-read');

    expect(ContentRead::query()->count())->toBe(0);
    expect((int) $contentItem->refresh()->reads_count)->toBe(0);
});

test('non published internal article does not track reads', function () {
    $contentItem = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Pending->value,
        'reads_count' => 0,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'tracking-pending',
        'title' => 'Tracking pending',
    ]);

    $this->get('/blog/fr/tracking-pending')
        ->assertNotFound();

    expect(ContentRead::query()->count())->toBe(0);
    expect((int) $contentItem->refresh()->reads_count)->toBe(0);
});
