<?php

use App\Enums\ContentStatus;
use App\Models\ContentItem;
use App\Models\ContentTranslation;

test('sitemap exposes static pages and published internal posts', function () {
    $publishedInternalPost = ContentItem::factory()->published()->internalPost()->create();
    ContentTranslation::factory()->for($publishedInternalPost)->forLocale('fr')->create([
        'slug' => 'sitemap-post',
        'title' => 'Sitemap post',
    ]);

    $draftInternalPost = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Draft->value,
    ]);
    ContentTranslation::factory()->for($draftInternalPost)->forLocale('fr')->create([
        'slug' => 'draft-post',
        'title' => 'Draft post',
    ]);

    $response = $this->get('/sitemap.xml');

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
    $response->assertHeader('X-Robots-Tag', 'noindex,follow');
    $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
    $response->assertSee(route('home'));
    $response->assertSee(route('blog.index'));
    $response->assertSee(route('about.show'));
    $response->assertSee(route('links.index'));
    $response->assertSee(route('blog.show', ['locale' => 'fr', 'slug' => 'sitemap-post']));
    $response->assertDontSee(route('blog.show', ['locale' => 'fr', 'slug' => 'draft-post']));
    $response->assertDontSee(route('forum.index'));
    $response->assertDontSee(route('seo.feed'));
});
