<?php

use App\Enums\ContentStatus;
use App\Models\ContentItem;
use App\Models\ContentTranslation;

test('rss feed exposes published content and excludes drafts', function () {
    $publishedInternalPost = ContentItem::factory()->published()->internalPost()->create();
    ContentTranslation::factory()->for($publishedInternalPost)->forLocale('fr')->create([
        'title' => 'Internal title',
        'slug' => 'internal-title',
        'excerpt' => 'Internal excerpt',
    ]);

    $publishedExternalPost = ContentItem::factory()->published()->externalPost()->create();
    ContentTranslation::factory()->for($publishedExternalPost)->forLocale('fr')->create([
        'title' => 'External title',
        'slug' => 'external-title',
        'excerpt' => 'External excerpt',
        'external_url' => 'https://example.com/external-article',
    ]);

    $draftInternalPost = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Draft->value,
    ]);
    ContentTranslation::factory()->for($draftInternalPost)->forLocale('fr')->create([
        'title' => 'Draft title',
        'slug' => 'draft-title',
        'excerpt' => 'Draft excerpt',
    ]);

    $response = $this->get('/feed.xml');

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
    $response->assertSee('<rss version="2.0">', false);
    $response->assertSee('Internal title');
    $response->assertSee('External title');
    $response->assertSee(route('blog.show', ['locale' => 'fr', 'slug' => 'internal-title']));
    $response->assertSee('https://example.com/external-article');
    $response->assertDontSee('Draft title');
});
