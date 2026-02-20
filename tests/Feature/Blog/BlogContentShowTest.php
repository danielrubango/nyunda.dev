<?php

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\ContentTranslation;

test('internal post can be rendered and markdown is sanitized', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'secure-internal-post',
        'title' => 'Secure internal post',
        'excerpt' => 'Internal excerpt',
        'body_markdown' => "# Heading\n\n<script>alert('xss')</script>\n\nParagraph",
    ]);

    $response = $this->get('/blog/fr/secure-internal-post');

    $response->assertSuccessful();
    $response->assertSee('Heading');
    $response->assertSee('Paragraph');
    $response->assertDontSee('<script>', false);
});

test('external post detail route redirects to external url', function () {
    $contentItem = ContentItem::factory()->published()->externalPost()->create();

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'external-post',
        'title' => 'External post',
        'excerpt' => 'External excerpt',
        'external_url' => 'https://example.com/article',
    ]);

    $response = $this->get('/blog/fr/external-post');

    $response->assertRedirect('https://example.com/article');
});

test('non published content cannot be displayed', function () {
    $pendingItem = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Pending->value,
        'type' => ContentType::InternalPost->value,
    ]);

    ContentTranslation::factory()->for($pendingItem)->forLocale('fr')->create([
        'slug' => 'pending-post',
        'title' => 'Pending post',
        'excerpt' => 'Pending excerpt',
    ]);

    $response = $this->get('/blog/fr/pending-post');

    $response->assertNotFound();
});
