<?php

use App\Actions\Content\ListLocalizedPublishedContentItems;
use App\Enums\ContentStatus;
use App\Models\ContentItem;
use App\Models\ContentTranslation;

test('can filter published content listing by a specific locale', function () {
    $itemWithFrenchAndEnglish = ContentItem::factory()->published()->create();
    $itemWithEnglishOnly = ContentItem::factory()->published()->create();
    $itemWithFrenchOnly = ContentItem::factory()->published()->create();
    ContentItem::factory()->create([
        'status' => ContentStatus::Draft->value,
    ]);

    ContentTranslation::factory()->for($itemWithFrenchAndEnglish)->forLocale('fr')->create();
    ContentTranslation::factory()->for($itemWithFrenchAndEnglish)->forLocale('en')->create();
    ContentTranslation::factory()->for($itemWithEnglishOnly)->forLocale('en')->create();
    ContentTranslation::factory()->for($itemWithFrenchOnly)->forLocale('fr')->create();

    $rows = app(ListLocalizedPublishedContentItems::class)->handle('en', 'fr');

    expect($rows)->toHaveCount(2)
        ->and($rows->pluck('translation.locale')->all())->toBe(['en', 'en']);
});

test('all languages mode selects user locale translation first', function () {
    $item = ContentItem::factory()->published()->create();

    ContentTranslation::factory()->for($item)->forLocale('fr')->create([
        'title' => 'Titre FR',
    ]);
    ContentTranslation::factory()->for($item)->forLocale('en')->create([
        'title' => 'Title EN',
    ]);

    $rows = app(ListLocalizedPublishedContentItems::class)->handle(null, 'en');

    expect($rows)->toHaveCount(1)
        ->and($rows->first()['translation']->locale)->toBe('en')
        ->and($rows->first()['translation']->title)->toBe('Title EN');
});

test('all languages mode falls back to french when user locale is missing', function () {
    $item = ContentItem::factory()->published()->create();

    ContentTranslation::factory()->for($item)->forLocale('fr')->create([
        'title' => 'Titre FR',
    ]);

    $rows = app(ListLocalizedPublishedContentItems::class)->handle(null, 'en');

    expect($rows)->toHaveCount(1)
        ->and($rows->first()['translation']->locale)->toBe('fr')
        ->and($rows->first()['translation']->title)->toBe('Titre FR');
});

test('all languages mode falls back to first available translation', function () {
    $item = ContentItem::factory()->published()->create();

    ContentTranslation::factory()->for($item)->forLocale('es')->create([
        'title' => 'Titulo ES',
    ]);

    $rows = app(ListLocalizedPublishedContentItems::class)->handle(null, 'en');

    expect($rows)->toHaveCount(1)
        ->and($rows->first()['translation']->locale)->toBe('es');
});

test('all languages mode returns one row per content item', function () {
    $firstItem = ContentItem::factory()->published()->create();
    $secondItem = ContentItem::factory()->published()->create();

    ContentTranslation::factory()->for($firstItem)->forLocale('fr')->create();
    ContentTranslation::factory()->for($firstItem)->forLocale('en')->create();
    ContentTranslation::factory()->for($secondItem)->forLocale('fr')->create();

    $rows = app(ListLocalizedPublishedContentItems::class)->handle(null, 'fr');

    expect($rows)->toHaveCount(2)
        ->and($rows->pluck('content_item.id')->unique()->count())->toBe(2);
});
