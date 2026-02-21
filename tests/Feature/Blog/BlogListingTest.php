<?php

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\ContentTranslation;

beforeEach(function (): void {
    app()->setLocale('fr');
});

test('blog listing supports locale and type filters', function () {
    $internal = ContentItem::factory()->published()->internalPost()->create();
    $external = ContentItem::factory()->published()->externalPost()->create();
    $frenchOnly = ContentItem::factory()->published()->internalPost()->create();
    $draft = ContentItem::factory()->create([
        'status' => ContentStatus::Draft->value,
        'type' => ContentType::InternalPost->value,
    ]);

    ContentTranslation::factory()->for($internal)->forLocale('en')->create([
        'title' => 'Internal EN',
        'excerpt' => 'Internal excerpt',
    ]);
    ContentTranslation::factory()->for($external)->forLocale('en')->create([
        'title' => 'External EN',
        'excerpt' => 'External excerpt',
        'external_url' => 'https://example.com/external',
    ]);
    ContentTranslation::factory()->for($frenchOnly)->forLocale('fr')->create([
        'title' => 'French only',
        'excerpt' => 'French excerpt',
    ]);
    ContentTranslation::factory()->for($draft)->forLocale('en')->create([
        'title' => 'Draft EN',
        'excerpt' => 'Draft excerpt',
    ]);

    $response = $this->get('/blog?locale=en&type=external_post');

    $response->assertSuccessful();
    $response->assertSee('External EN');
    $response->assertSee('target="_blank"', false);
    $response->assertDontSee('Internal EN');
    $response->assertDontSee('French only');
    $response->assertDontSee('Draft EN');
});

test('all languages mode selects user locale then falls back to french', function () {
    $withBothLocales = ContentItem::factory()->published()->internalPost()->create();
    $withFrenchOnly = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($withBothLocales)->forLocale('fr')->create([
        'title' => 'Titre FR',
        'excerpt' => 'Excerpt FR',
    ]);
    ContentTranslation::factory()->for($withBothLocales)->forLocale('en')->create([
        'title' => 'Title EN',
        'excerpt' => 'Excerpt EN',
    ]);
    ContentTranslation::factory()->for($withFrenchOnly)->forLocale('fr')->create([
        'title' => 'Fallback FR',
        'excerpt' => 'Fallback excerpt',
    ]);

    $response = $this->withHeaders([
        'Accept-Language' => 'en',
    ])->get('/blog?locale=all');

    $response->assertSuccessful();
    $response->assertSee('Title EN');
    $response->assertSee('Fallback FR');
    $response->assertDontSee('Titre FR');
});

test('blog listing keeps selected filters between requests until reset', function () {
    $internal = ContentItem::factory()->published()->internalPost()->create();
    $external = ContentItem::factory()->published()->externalPost()->create();

    ContentTranslation::factory()->for($internal)->forLocale('fr')->create([
        'title' => 'Interne FR',
        'excerpt' => 'Extrait interne',
    ]);
    ContentTranslation::factory()->for($internal)->forLocale('en')->create([
        'title' => 'Internal EN',
        'excerpt' => 'Internal excerpt',
    ]);

    ContentTranslation::factory()->for($external)->forLocale('fr')->create([
        'title' => 'Externe FR',
        'excerpt' => 'Extrait externe',
        'external_url' => 'https://example.com/fr',
    ]);
    ContentTranslation::factory()->for($external)->forLocale('en')->create([
        'title' => 'External EN',
        'excerpt' => 'External excerpt',
        'external_url' => 'https://example.com/en',
    ]);

    $filteredResponse = $this->get('/blog?locale=en&type=external_post&q=External');
    $filteredResponse->assertSuccessful();
    $filteredResponse->assertSee('External EN');
    $filteredResponse->assertDontSee('Internal EN');

    $persistedResponse = $this->get('/blog');
    $persistedResponse->assertSuccessful();
    $persistedResponse->assertSee('External EN');
    $persistedResponse->assertDontSee('Internal EN');

    $resetResponse = $this->get('/blog?reset=1');
    $resetResponse->assertSuccessful();
    $resetResponse->assertSee('Internal EN');
    $resetResponse->assertSee('External EN');
});
