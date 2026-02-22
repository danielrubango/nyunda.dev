<?php

use App\Actions\Content\ShareOnSocialNetworks;
use App\Enums\ContentStatus;
use App\Jobs\ShareOnSocialNetworksJob;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

test('share job is dispatched when content transitions to published with share enabled', function () {
    Queue::fake();

    $contentItem = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Draft->value,
        'share_on_publish' => true,
    ]);

    $contentItem->update([
        'status' => ContentStatus::Published->value,
        'published_at' => now(),
    ]);

    Queue::assertPushed(ShareOnSocialNetworksJob::class, function (ShareOnSocialNetworksJob $job) use ($contentItem): bool {
        return $job->contentItemId === $contentItem->id;
    });
});

test('share job is not dispatched when share_on_publish is disabled', function () {
    Queue::fake();

    $contentItem = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Draft->value,
        'share_on_publish' => false,
    ]);

    $contentItem->update([
        'status' => ContentStatus::Published->value,
        'published_at' => now(),
    ]);

    Queue::assertNotPushed(ShareOnSocialNetworksJob::class);
});

test('share job is dispatched only when scheduled content actually becomes published', function () {
    Queue::fake();
    Carbon::setTestNow(Carbon::parse('2026-02-22 10:00:00'));

    try {
        $contentItem = ContentItem::factory()->internalPost()->create([
            'status' => ContentStatus::Pending->value,
            'share_on_publish' => true,
            'published_at' => now()->addHour(),
        ]);

        $this->artisan('content-items:publish-scheduled')->assertSuccessful();
        Queue::assertNotPushed(ShareOnSocialNetworksJob::class);

        Carbon::setTestNow(now()->addHours(2));

        $this->artisan('content-items:publish-scheduled')->assertSuccessful();

        Queue::assertPushed(ShareOnSocialNetworksJob::class, function (ShareOnSocialNetworksJob $job) use ($contentItem): bool {
            return $job->contentItemId === $contentItem->id;
        });
    } finally {
        Carbon::setTestNow();
    }
});

test('social sharing action logs successful attempts for x and linkedin', function () {
    config()->set('social.post_template', ':title — :url');
    config()->set('social.hashtags', ['laravel', 'php']);
    config()->set('social.x.enabled', true);
    config()->set('social.x.bearer_token', 'x-token');
    config()->set('social.x.api_url', 'https://social.test/x');
    config()->set('social.linkedin.enabled', true);
    config()->set('social.linkedin.access_token', 'li-token');
    config()->set('social.linkedin.author_urn', 'urn:li:person:123');
    config()->set('social.linkedin.api_url', 'https://social.test/linkedin');

    Http::fake([
        'https://social.test/x' => Http::response(['id' => 'x-1'], 201),
        'https://social.test/linkedin' => Http::response(['id' => 'li-1'], 201),
    ]);

    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'share_on_publish' => true,
    ]);
    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Publication technique',
        'slug' => 'publication-technique',
        'excerpt' => 'Extrait',
    ]);

    app(ShareOnSocialNetworks::class)->handle($contentItem->fresh(['translations']));

    $this->assertDatabaseHas('social_share_logs', [
        'content_item_id' => $contentItem->id,
        'platform' => 'x',
        'status' => 'success',
    ]);
    $this->assertDatabaseHas('social_share_logs', [
        'content_item_id' => $contentItem->id,
        'platform' => 'linkedin',
        'status' => 'success',
    ]);

    Http::assertSentCount(2);
});

test('social sharing throws and logs failure when a platform request fails', function () {
    config()->set('social.x.enabled', true);
    config()->set('social.x.bearer_token', 'x-token');
    config()->set('social.x.api_url', 'https://social.test/x-fail');
    config()->set('social.linkedin.enabled', false);

    Http::fake([
        'https://social.test/x-fail' => Http::response(['error' => 'fail'], 500),
    ]);

    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'share_on_publish' => true,
    ]);
    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Publication en echec',
        'slug' => 'publication-echec',
        'excerpt' => 'Extrait',
    ]);

    expect(fn () => app(ShareOnSocialNetworks::class)->handle($contentItem->fresh(['translations'])))
        ->toThrow(\RuntimeException::class);

    $this->assertDatabaseHas('social_share_logs', [
        'content_item_id' => $contentItem->id,
        'platform' => 'x',
        'status' => 'failed',
    ]);
});
