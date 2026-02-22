<?php

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\ContentItem;
use Illuminate\Support\Carbon;

test('scheduled publication command publishes due pending items for all editorial types', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-22 10:00:00'));

    try {
        $scheduledAt = now()->subMinute();

        $internalPost = ContentItem::factory()->internalPost()->create([
            'status' => ContentStatus::Pending->value,
            'approved_at' => null,
            'published_at' => $scheduledAt,
        ]);
        $externalPost = ContentItem::factory()->externalPost()->create([
            'status' => ContentStatus::Pending->value,
            'approved_at' => null,
            'published_at' => $scheduledAt,
        ]);
        $communityLink = ContentItem::factory()->create([
            'type' => ContentType::CommunityLink->value,
            'status' => ContentStatus::Pending->value,
            'approved_at' => null,
            'published_at' => $scheduledAt,
            'show_likes' => false,
            'show_comments' => false,
        ]);

        $this->artisan('content-items:publish-scheduled')
            ->expectsOutput('Published 3 scheduled content item(s).')
            ->assertSuccessful();

        foreach ([$internalPost, $externalPost, $communityLink] as $contentItem) {
            $refreshed = $contentItem->refresh();

            expect($refreshed->status)->toBe(ContentStatus::Published);
            expect($refreshed->approved_at)->not->toBeNull();
            expect($refreshed->published_at?->equalTo($scheduledAt))->toBeTrue();
        }
    } finally {
        Carbon::setTestNow();
    }
});

test('scheduled publication command does not publish items before schedule', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-22 10:00:00'));

    try {
        $scheduledAt = now()->addHour();

        $contentItem = ContentItem::factory()->internalPost()->create([
            'status' => ContentStatus::Pending->value,
            'approved_at' => null,
            'published_at' => $scheduledAt,
        ]);

        $this->artisan('content-items:publish-scheduled')
            ->expectsOutput('Published 0 scheduled content item(s).')
            ->assertSuccessful();

        $contentItem->refresh();

        expect($contentItem->status)->toBe(ContentStatus::Pending);
        expect($contentItem->approved_at)->toBeNull();
        expect($contentItem->published_at?->equalTo($scheduledAt))->toBeTrue();
    } finally {
        Carbon::setTestNow();
    }
});
