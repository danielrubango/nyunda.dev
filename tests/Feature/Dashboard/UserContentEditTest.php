<?php

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\User;

test('user can open edit page for own content', function () {
    $user = User::factory()->create();

    $contentItem = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Pending->value,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Mon article editable',
        'slug' => 'mon-article-editable',
        'body_markdown' => 'Contenu initial',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.content.edit', ['contentItem' => $contentItem]));

    $response->assertSuccessful();
    $response->assertSee('Modifier mon contenu');
    $response->assertSee('Mon article editable');
    $response->assertSee('← Retour a mes contenus');
});

test('user cannot open edit page for another user content', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $contentItem = ContentItem::factory()->internalPost()->create([
        'author_id' => $otherUser->id,
        'status' => ContentStatus::Pending->value,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Article prive',
        'slug' => 'article-prive',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.content.edit', ['contentItem' => $contentItem]))
        ->assertNotFound();
});

test('user can update own content from dashboard', function () {
    $user = User::factory()->create();

    $contentItem = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Pending->value,
    ]);

    $translation = ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Titre initial',
        'slug' => 'titre-initial',
        'body_markdown' => 'Version initiale',
    ]);

    $response = $this->actingAs($user)->put(route('dashboard.content.update', ['contentItem' => $contentItem]), [
        'type' => ContentType::InternalPost->value,
        'locale' => 'fr',
        'title' => 'Titre mis a jour dashboard',
        'excerpt' => 'Nouvel extrait',
        'body_markdown' => '## Contenu mis a jour',
        'external_url' => null,
        'external_site_name' => null,
        'external_description' => null,
        'featured_image_url' => 'https://example.com/new-image.jpg',
    ]);

    $response->assertRedirect(route('dashboard.content.index'));

    $contentItem->refresh();
    $translation->refresh();

    expect($contentItem->type)->toBe(ContentType::InternalPost)
        ->and($translation->title)->toBe('Titre mis a jour dashboard')
        ->and($translation->excerpt)->toBe('Nouvel extrait')
        ->and($translation->body_markdown)->toBe('## Contenu mis a jour')
        ->and($translation->featured_image_url)->toBe('https://example.com/new-image.jpg');
});

test('user cannot update another user content', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $contentItem = ContentItem::factory()->internalPost()->create([
        'author_id' => $otherUser->id,
        'status' => ContentStatus::Pending->value,
    ]);

    $translation = ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Titre protege',
        'slug' => 'titre-protege',
        'body_markdown' => 'Version protegee',
    ]);

    $this->actingAs($user)
        ->put(route('dashboard.content.update', ['contentItem' => $contentItem]), [
            'type' => ContentType::InternalPost->value,
            'locale' => 'fr',
            'title' => 'Tentative non autorisee',
            'excerpt' => 'Tentative',
            'body_markdown' => 'Nope',
        ])
        ->assertNotFound();

    $translation->refresh();

    expect($translation->title)->toBe('Titre protege')
        ->and($translation->body_markdown)->toBe('Version protegee');
});
