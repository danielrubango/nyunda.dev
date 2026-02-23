<?php

use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\User;

test('about page defaults to french copy even when accept language is english', function () {
    $response = $this->withHeaders([
        'Accept-Language' => 'en',
    ])->get(route('about.show'));

    $response->assertSuccessful();
    $response->assertSee('Je suis un développeur logiciel Full-Stack avec plus de six ans d’expérience en développement web et en systèmes IT.');
    $response->assertDontSee('I am a Full-Stack Software Developer with over six years of experience in web development and IT systems.');
});

test('locale can be switched manually from the locale endpoint', function () {
    $this->from(route('about.show'))
        ->post(route('locale.update'), [
            'locale' => 'en',
        ])
        ->assertRedirect(route('about.show'));

    $response = $this->get(route('about.show'));

    $response->assertSuccessful();
    $response->assertSee('I am a Full-Stack Software Developer with over six years of experience in web development and IT systems.');
    $response->assertDontSee('Je suis un développeur logiciel Full-Stack avec plus de six ans d’expérience en développement web et en systèmes IT.');
});

test('community link create page uses user preferred locale copy', function () {
    $user = User::factory()->create([
        'preferred_locale' => 'en',
    ]);

    $response = $this->actingAs($user)->get(route('community-links.create'));

    $response->assertSuccessful();
    $response->assertSee('Submit a community link');
    $response->assertSee('Language');
    $response->assertDontSee('Soumettre un lien communautaire');
});

test('locale change on article switches to target translation when available', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    $frenchTranslation = ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'article-locale-fr',
        'title' => 'Article FR',
        'excerpt' => 'Extrait FR',
    ]);

    $englishTranslation = ContentTranslation::factory()->for($contentItem)->forLocale('en')->create([
        'slug' => 'article-locale-en',
        'title' => 'Article EN',
        'excerpt' => 'Excerpt EN',
    ]);

    $response = $this->from(route('blog.show', [
        'locale' => $frenchTranslation->locale,
        'slug' => $frenchTranslation->slug,
    ]))->post(route('locale.update'), [
        'locale' => 'en',
        'current_content_locale' => 'fr',
        'current_content_slug' => $frenchTranslation->slug,
    ]);

    $response->assertRedirect(route('blog.show', [
        'locale' => $englishTranslation->locale,
        'slug' => $englishTranslation->slug,
    ]));
    $response->assertSessionHas('preferred_locale', 'en');
});

test('locale change on article keeps current article when target translation is missing', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    $frenchTranslation = ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'article-locale-fr-only',
        'title' => 'Article FR uniquement',
        'excerpt' => 'Extrait FR uniquement',
    ]);

    $response = $this->from(route('blog.show', [
        'locale' => $frenchTranslation->locale,
        'slug' => $frenchTranslation->slug,
    ]))->post(route('locale.update'), [
        'locale' => 'en',
        'current_content_locale' => 'fr',
        'current_content_slug' => $frenchTranslation->slug,
    ]);

    $response->assertRedirect(route('blog.show', [
        'locale' => $frenchTranslation->locale,
        'slug' => $frenchTranslation->slug,
    ]));
    $response->assertSessionHas('preferred_locale', 'en');

    $followedResponse = $this->withSession([
        'preferred_locale' => 'en',
    ])->get(route('blog.show', [
        'locale' => $frenchTranslation->locale,
        'slug' => $frenchTranslation->slug,
    ]));

    $followedResponse->assertSuccessful();
    $followedResponse->assertSee('Links');
    $followedResponse->assertDontSee('Liens');
    $followedResponse->assertSee('Article FR uniquement');
});
