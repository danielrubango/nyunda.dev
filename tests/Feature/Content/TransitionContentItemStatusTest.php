<?php

use App\Actions\Content\TransitionContentItemStatus;
use App\Enums\ContentStatus;
use App\Models\ContentItem;
use Illuminate\Support\Carbon;

test('transition action publishes a content item and sets timestamps', function () {
    $contentItem = ContentItem::factory()->create([
        'status' => ContentStatus::Pending->value,
        'approved_at' => null,
        'published_at' => null,
    ]);

    app(TransitionContentItemStatus::class)->handle($contentItem, ContentStatus::Published);

    $contentItem->refresh();

    expect($contentItem->status)->toBe(ContentStatus::Published);
    expect($contentItem->approved_at)->not->toBeNull();
    expect($contentItem->published_at)->not->toBeNull();
});

test('transition action moving away from published clears publication timestamps', function () {
    $contentItem = ContentItem::factory()->published()->create();

    app(TransitionContentItemStatus::class)->handle($contentItem, ContentStatus::Draft);

    $contentItem->refresh();

    expect($contentItem->status)->toBe(ContentStatus::Draft);
    expect($contentItem->approved_at)->toBeNull();
    expect($contentItem->published_at)->toBeNull();
});

test('transition action can schedule a pending content item with a publication datetime', function () {
    $contentItem = ContentItem::factory()->create([
        'status' => ContentStatus::Draft->value,
        'approved_at' => null,
        'published_at' => null,
    ]);
    $scheduledAt = Carbon::parse('2026-02-23 08:15:00');

    app(TransitionContentItemStatus::class)->handle(
        $contentItem,
        ContentStatus::Pending,
        publishedAt: $scheduledAt,
    );

    $contentItem->refresh();

    expect($contentItem->status)->toBe(ContentStatus::Pending);
    expect($contentItem->approved_at)->toBeNull();
    expect($contentItem->published_at?->equalTo($scheduledAt))->toBeTrue();
});
