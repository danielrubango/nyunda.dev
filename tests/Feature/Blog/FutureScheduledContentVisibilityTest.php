<?php

use App\Enums\ContentStatus;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Support\Carbon;

test('future scheduled published content cannot be displayed before due time and becomes visible after', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-22 10:00:00'));

    try {
        $contentItem = ContentItem::factory()->internalPost()->create([
            'status' => ContentStatus::Published->value,
            'published_at' => now()->addHour(),
        ]);

        ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
            'slug' => 'future-scheduled-post',
            'title' => 'Future scheduled post',
            'excerpt' => 'Future scheduled excerpt',
        ]);

        $beforeDueResponse = $this->get('/blog/fr/future-scheduled-post');
        $beforeDueResponse->assertNotFound();

        Carbon::setTestNow(now()->addHours(2));

        $afterDueResponse = $this->get('/blog/fr/future-scheduled-post');
        $afterDueResponse->assertSuccessful();
        $afterDueResponse->assertSee('Future scheduled post');
    } finally {
        Carbon::setTestNow();
    }
});
