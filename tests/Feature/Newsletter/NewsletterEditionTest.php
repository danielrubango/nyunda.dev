<?php

use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\NewsletterEdition;

test('newsletter:prepare-monthly creates a draft edition', function () {
    ContentItem::factory()
        ->count(3)
        ->published()
        ->internalPost()
        ->has(ContentTranslation::factory(), 'translations')
        ->create();

    $this->artisan('newsletter:prepare-monthly')
        ->assertSuccessful();

    expect(NewsletterEdition::count())->toBe(1);

    $edition = NewsletterEdition::first();
    expect($edition->status)->toBe('draft');
    expect($edition->content_item_ids)->toHaveCount(3);
    expect($edition->subject_fr)->toStartWith('Newsletter NYUNDA.DEV');
    expect($edition->subject_en)->toStartWith('NYUNDA.DEV Newsletter');
});

test('newsletter:prepare-monthly warns if a draft already exists this month', function () {
    NewsletterEdition::factory()->create([
        'status' => 'draft',
        'created_at' => now(),
    ]);

    $this->artisan('newsletter:prepare-monthly')
        ->assertFailed();

    expect(NewsletterEdition::count())->toBe(1); // pas de doublon
});

test('newsletter:prepare-monthly --force creates another draft even if one exists', function () {
    ContentItem::factory()
        ->published()
        ->internalPost()
        ->has(ContentTranslation::factory(), 'translations')
        ->create();

    NewsletterEdition::factory()->create(['status' => 'draft']);

    $this->artisan('newsletter:prepare-monthly --force')
        ->assertSuccessful();

    expect(NewsletterEdition::count())->toBe(2);
});

test('newsletter:prepare-monthly fails when no published articles with translations exist', function () {
    ContentItem::factory()->count(3)->create(); // brouillons sans traduction

    $this->artisan('newsletter:prepare-monthly')
        ->assertFailed();

    expect(NewsletterEdition::count())->toBe(0);
});
