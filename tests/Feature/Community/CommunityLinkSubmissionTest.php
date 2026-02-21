<?php

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Jobs\FetchCommunityLinkMetadataJob;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

test('authenticated user can submit a community link and it stays pending', function () {
    $user = User::factory()->create();
    Queue::fake();

    $response = $this->actingAs($user)->post(route('community-links.store'), [
        'locale' => 'fr',
        'title' => 'Une ressource Laravel à suivre',
        'excerpt' => 'Un article utile pour suivre les bonnes pratiques Laravel.',
        'external_url' => 'https://example.com/laravel-article',
        'external_site_name' => 'Example',
        'external_description' => 'Description distante',
    ]);

    $response->assertRedirect(route('blog.index'));

    $contentItem = ContentItem::query()
        ->where('author_id', $user->id)
        ->latest('id')
        ->first();

    expect($contentItem)->not->toBeNull()
        ->and($contentItem->type)->toBe(ContentType::ExternalPost)
        ->and($contentItem->status)->toBe(ContentStatus::Pending)
        ->and($contentItem->show_comments)->toBeFalse()
        ->and($contentItem->show_likes)->toBeFalse();

    $this->assertDatabaseHas('content_translations', [
        'content_item_id' => $contentItem->id,
        'locale' => 'fr',
        'title' => 'Une ressource Laravel à suivre',
        'external_url' => 'https://example.com/laravel-article',
    ]);

    Queue::assertPushed(FetchCommunityLinkMetadataJob::class);
});

test('community link submission remains pending for admins too', function () {
    $admin = User::factory()->admin()->create();
    Queue::fake();

    $response = $this->actingAs($admin)->post(route('community-links.store'), [
        'locale' => 'en',
        'title' => 'Admin submitted link',
        'excerpt' => 'This should still be pending approval.',
        'external_url' => 'https://example.com/admin-link',
    ]);

    $response->assertRedirect(route('blog.index'));

    $this->assertDatabaseHas('content_items', [
        'author_id' => $admin->id,
        'type' => ContentType::ExternalPost->value,
        'status' => ContentStatus::Pending->value,
    ]);
});

test('guest cannot access community link submission endpoints', function () {
    $this->get(route('community-links.create'))->assertRedirect(route('login'));
    $this->post(route('community-links.store'), [])->assertRedirect(route('login'));
});

test('authenticated user can open community link submission form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('community-links.create'));

    $response->assertSuccessful();
    $response->assertSee('Soumettre un lien communautaire');
});

test('community link submission validates required payload', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('community-links.store'), [
        'locale' => 'de',
        'title' => 'abc',
        'excerpt' => 'court',
        'external_url' => 'not-a-url',
    ]);

    $response->assertSessionHasErrors([
        'locale',
        'title',
        'excerpt',
        'external_url',
    ]);
});

test('pending external links are not listed publicly', function () {
    $contentItem = ContentItem::factory()->externalPost()->create([
        'status' => ContentStatus::Pending->value,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Pending community item',
        'excerpt' => 'Pending excerpt',
        'external_url' => 'https://example.com/pending-community',
    ]);

    $response = $this->get(route('blog.index'));

    $response->assertSuccessful();
    $response->assertDontSee('Pending community item');
});

test('metadata job updates title and excerpt when they are omitted', function () {
    Http::fake([
        'https://example.com/auto-metadata' => Http::response(<<<'HTML'
            <html>
                <head>
                    <meta property="og:title" content="Titre OG complet">
                    <meta property="og:description" content="Description OG enrichie pour ce lien partagé.">
                    <meta property="og:site_name" content="Example Site">
                    <meta property="og:image" content="https://example.com/image.jpg">
                </head>
            </html>
        HTML),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)->post(route('community-links.store'), [
        'locale' => 'fr',
        'external_url' => 'https://example.com/auto-metadata',
    ])->assertRedirect(route('blog.index'));

    $translation = ContentTranslation::query()
        ->latest('id')
        ->first();

    expect($translation)->not->toBeNull();

    $job = new FetchCommunityLinkMetadataJob(
        contentTranslationId: $translation->id,
        shouldUpdateTitle: true,
        shouldUpdateExcerpt: true,
    );
    $job->handle(app(\App\Actions\Content\FetchOpenGraphData::class));

    $translation->refresh();

    expect($translation->title)->toBe('Titre OG complet')
        ->and($translation->excerpt)->toBe('Description OG enrichie pour ce lien partagé.')
        ->and($translation->external_site_name)->toBe('Example Site')
        ->and($translation->external_og_image_url)->toBe('https://example.com/image.jpg');
});
