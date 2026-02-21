<?php

use App\Actions\Content\CreateInitialTranslationForContentItem;
use App\Enums\ContentType;
use App\Models\ContentItem;

test('initial translation action creates slug and excerpt automatically', function () {
    $contentItem = ContentItem::factory()->create([
        'type' => ContentType::InternalPost->value,
    ]);

    $translation = app(CreateInitialTranslationForContentItem::class)->handle($contentItem, [
        'initial_locale' => 'fr',
        'initial_title' => 'Mon premier article technique',
        'initial_slug' => '',
        'initial_excerpt' => '',
        'initial_body_markdown' => '## Introduction'.PHP_EOL.PHP_EOL.'Contenu d exemple pour le résumé automatique.',
    ]);

    expect($translation)->not->toBeNull();
    expect($translation?->content_item_id)->toBe($contentItem->id);
    expect($translation?->locale)->toBe('fr');
    expect($translation?->slug)->toBe('mon-premier-article-technique');
    expect($translation?->excerpt)->not->toBe('');
});

test('initial translation action returns null when title is missing', function () {
    $contentItem = ContentItem::factory()->create([
        'type' => ContentType::ExternalPost->value,
    ]);

    $translation = app(CreateInitialTranslationForContentItem::class)->handle($contentItem, [
        'initial_title' => '',
    ]);

    expect($translation)->toBeNull();
});
