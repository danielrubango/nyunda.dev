<?php

use App\Enums\ContentStatus;
use App\Models\Comment;
use App\Models\ContentItem;
use App\Models\ContentLike;
use App\Models\ContentTranslation;
use App\Models\LinkVote;
use App\Models\User;

test('dashboard content index shows personal rows stats and status labels', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $published = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Published->value,
        'approved_at' => now(),
        'published_at' => now()->subDay(),
        'reads_count' => 12,
    ]);

    $scheduled = ContentItem::factory()->externalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Pending->value,
        'published_at' => now()->addDay(),
        'reads_count' => 5,
    ]);

    $pending = ContentItem::factory()->communityLink()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Pending->value,
        'published_at' => null,
        'reads_count' => 3,
    ]);

    $rejected = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Rejected->value,
        'reads_count' => 1,
    ]);

    $otherItem = ContentItem::factory()->internalPost()->create([
        'author_id' => $otherUser->id,
        'status' => ContentStatus::Published->value,
    ]);

    ContentTranslation::factory()->for($published)->forLocale('fr')->create(['title' => 'Mon contenu publie', 'slug' => 'mon-contenu-publie']);
    ContentTranslation::factory()->for($scheduled)->forLocale('fr')->create(['title' => 'Mon contenu planifie', 'slug' => 'mon-contenu-planifie', 'external_url' => 'https://example.com/scheduled']);
    ContentTranslation::factory()->for($pending)->forLocale('fr')->create(['title' => 'Mon contenu en attente', 'slug' => 'mon-contenu-pending', 'external_url' => 'https://example.com/pending']);
    ContentTranslation::factory()->for($rejected)->forLocale('fr')->create(['title' => 'Mon contenu rejete', 'slug' => 'mon-contenu-rejete']);
    ContentTranslation::factory()->for($otherItem)->forLocale('fr')->create(['title' => 'Contenu autre utilisateur', 'slug' => 'contenu-autre-utilisateur']);

    Comment::factory()->create(['content_item_id' => $published->id, 'user_id' => $otherUser->id]);
    Comment::factory()->create(['content_item_id' => $pending->id, 'user_id' => $otherUser->id]);

    ContentLike::factory()->create(['content_item_id' => $published->id, 'user_id' => $otherUser->id]);
    LinkVote::query()->create(['content_item_id' => $pending->id, 'user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('dashboard.content.index'));

    $response->assertSuccessful();
    $response->assertSee('Mon contenu publie');
    $response->assertSee('Mon contenu planifie');
    $response->assertSee('Mon contenu en attente');
    $response->assertSee('Mon contenu rejete');
    $response->assertDontSee('Contenu autre utilisateur');
    $response->assertSee('Accepte et publie');
    $response->assertSee('Accepte et planifie');
    $response->assertSee('En attente d acceptation');
    $response->assertSee('Rejete');
    $response->assertSee('21');
    $response->assertSee(route('dashboard.content.edit', ['contentItem' => $published]), false);
    $response->assertSee('data-test="dashboard-content-datatable"', false);
    $response->assertSee('← Retour au dashboard');
});

test('dashboard content index supports status filter', function () {
    $user = User::factory()->create();

    $published = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Published->value,
        'published_at' => now(),
    ]);

    $pending = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Pending->value,
    ]);

    ContentTranslation::factory()->for($published)->forLocale('fr')->create(['title' => 'Contenu publie filtre', 'slug' => 'contenu-publie-filtre']);
    ContentTranslation::factory()->for($pending)->forLocale('fr')->create(['title' => 'Contenu pending filtre', 'slug' => 'contenu-pending-filtre']);

    $response = $this->actingAs($user)->get(route('dashboard.content.index', [
        'status' => ContentStatus::Published->value,
    ]));

    $response->assertSuccessful();
    $response->assertSee('Contenu publie filtre');
    $response->assertDontSee('Contenu pending filtre');
});

test('dashboard activity comments route redirects to content index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.activity.comments'));

    $response->assertRedirect(route('dashboard.content.index'));
});
