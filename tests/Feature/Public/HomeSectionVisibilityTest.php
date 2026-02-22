<?php

use App\Models\ContentItem;
use App\Models\ContentTranslation;

test('home page hides recent articles section when only one article is available', function () {
    $singleArticle = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($singleArticle)->forLocale('fr')->create([
        'title' => 'Unique article',
        'excerpt' => 'Extrait unique',
        'slug' => 'unique-article',
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Unique article');
    $response->assertDontSee('Articles récents');
});

test('home page hides links section when there are no link rows', function () {
    $internal = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($internal)->forLocale('fr')->create([
        'title' => 'Article interne sans lien',
        'excerpt' => 'Extrait interne sans lien',
        'slug' => 'article-interne-sans-lien',
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('Liens récents');
    $response->assertDontSee('Voir tous les liens');
});

test('home page hides dynamic sections when there is no published content', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('Articles récents');
    $response->assertDontSee('Articles populaires');
    $response->assertDontSee('Liens récents');
});
