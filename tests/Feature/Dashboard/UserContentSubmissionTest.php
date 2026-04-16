<?php

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\User;

test('regular user submission is stored as pending', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.content.store'), [
        'type' => ContentType::InternalPost->value,
        'locale' => 'fr',
        'title' => 'Article interne utilisateur',
        'excerpt' => 'Extrait utilisateur',
        'body_markdown' => 'Contenu interne en markdown.',
    ]);

    $response->assertRedirect(route('dashboard'));

    $contentItem = ContentItem::query()->latest('id')->first();

    expect($contentItem)->not->toBeNull();
    expect($contentItem->type)->toBe(ContentType::InternalPost)
        ->and($contentItem->status)->toBe(ContentStatus::Pending)
        ->and($contentItem->author_id)->toBe($user->id);
});

test('author submission is published directly', function () {
    $author = User::factory()->author()->create();

    $response = $this->actingAs($author)->post(route('dashboard.content.store'), [
        'type' => ContentType::ExternalPost->value,
        'locale' => 'fr',
        'title' => 'Ressource externe auteur',
        'excerpt' => 'Extrait auteur',
        'external_url' => 'https://example.com/external-author',
    ]);

    $response->assertRedirect(route('dashboard'));

    $contentItem = ContentItem::query()->latest('id')->first();

    expect($contentItem)->not->toBeNull();
    expect($contentItem->type)->toBe(ContentType::ExternalPost)
        ->and($contentItem->status)->toBe(ContentStatus::Published)
        ->and($contentItem->author_id)->toBe($author->id);
});

test('community link submissions are stored with community_link type', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.content.store'), [
        'type' => ContentType::CommunityLink->value,
        'locale' => 'fr',
        'title' => 'Lien de communaute',
        'excerpt' => 'Extrait communaute',
        'external_url' => 'https://example.com/community-link',
        'external_description' => 'Description de la ressource externe',
    ]);

    $response->assertRedirect(route('dashboard'));

    $contentItem = ContentItem::query()->latest('id')->first();

    expect($contentItem)->not->toBeNull();
    expect($contentItem->type)->toBe(ContentType::CommunityLink)
        ->and($contentItem->status)->toBe(ContentStatus::Pending)
        ->and($contentItem->author_id)->toBe($user->id);
});
