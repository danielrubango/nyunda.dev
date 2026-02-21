<?php

use App\Models\ContentItem;
use App\Models\ContentRead;
use App\Models\User;
use Illuminate\Support\Carbon;

test('content reads prune command deletes logs older than 90 days only', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'reads_count' => 7,
    ]);
    $user = User::factory()->create();

    $oldRead = ContentRead::query()->create([
        'content_item_id' => $contentItem->id,
        'user_id' => $user->id,
        'visitor_fingerprint' => null,
        'counted_at' => now()->subDays(91),
    ]);

    $recentRead = ContentRead::query()->create([
        'content_item_id' => $contentItem->id,
        'user_id' => $user->id,
        'visitor_fingerprint' => null,
        'counted_at' => now()->subDays(20),
    ]);

    $this->artisan('content-reads:prune')
        ->assertSuccessful();

    $this->assertDatabaseMissing('content_reads', ['id' => $oldRead->id]);
    $this->assertDatabaseHas('content_reads', ['id' => $recentRead->id]);
    expect((int) $contentItem->refresh()->reads_count)->toBe(7);
});

test('content reads prune command keeps logs at 90 day boundary', function () {
    $fixedNow = Carbon::parse('2026-02-21 12:00:00');
    Carbon::setTestNow($fixedNow);

    try {
        $contentItem = ContentItem::factory()->published()->internalPost()->create();
        $user = User::factory()->create();

        $boundaryRead = ContentRead::query()->create([
            'content_item_id' => $contentItem->id,
            'user_id' => $user->id,
            'visitor_fingerprint' => null,
            'counted_at' => $fixedNow->copy()->subDays(90),
        ]);

        $this->artisan('content-reads:prune')
            ->assertSuccessful();

        $this->assertDatabaseHas('content_reads', ['id' => $boundaryRead->id]);
    } finally {
        Carbon::setTestNow();
    }
});
