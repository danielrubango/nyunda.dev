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

    $draft = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Draft->value,
        'reads_count' => 0,
    ]);

    $otherItem = ContentItem::factory()->internalPost()->create([
        'author_id' => $otherUser->id,
        'status' => ContentStatus::Published->value,
    ]);

    ContentTranslation::factory()->for($published)->forLocale('fr')->create(['title' => 'Mon contenu publie', 'slug' => 'mon-contenu-publie']);
    ContentTranslation::factory()->for($scheduled)->forLocale('fr')->create(['title' => 'Mon contenu planifie', 'slug' => 'mon-contenu-planifie', 'external_url' => 'https://example.com/scheduled']);
    ContentTranslation::factory()->for($pending)->forLocale('fr')->create(['title' => 'Mon contenu en attente', 'slug' => 'mon-contenu-pending', 'external_url' => 'https://example.com/pending']);
    ContentTranslation::factory()->for($rejected)->forLocale('fr')->create(['title' => 'Mon contenu rejete', 'slug' => 'mon-contenu-rejete']);
    ContentTranslation::factory()->for($draft)->forLocale('fr')->create(['title' => 'Mon contenu brouillon', 'slug' => 'mon-contenu-brouillon']);
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
    $response->assertSee('Mon contenu brouillon');
    $response->assertDontSee('Contenu autre utilisateur');
    $response->assertSee('Publie');
    $response->assertSee('Accepte');
    $response->assertSee('En attente');
    $response->assertSee('Rejete');
    $response->assertSee('Brouillon');
    $response->assertSee('21');
    $response->assertSee(route('dashboard.content.edit', ['contentItem' => $published]), false);
    $response->assertSee('data-test="dashboard-content-datatable"', false);
    $response->assertSee('data-row-link="'.route('dashboard.content.edit', ['contentItem' => $published]).'"', false);
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

test('dashboard content index exposes sortable header links and sorts by reads', function () {
    $user = User::factory()->create();

    $lowReadsItem = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Published->value,
        'reads_count' => 3,
        'published_at' => now(),
    ]);

    $highReadsItem = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Published->value,
        'reads_count' => 25,
        'published_at' => now(),
    ]);

    ContentTranslation::factory()->for($lowReadsItem)->forLocale('fr')->create([
        'title' => 'Article faible vue',
        'slug' => 'article-faible-vue',
    ]);
    ContentTranslation::factory()->for($highReadsItem)->forLocale('fr')->create([
        'title' => 'Article forte vue',
        'slug' => 'article-forte-vue',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.content.index', [
        'sort' => 'reads',
        'direction' => 'asc',
    ]));

    $response->assertSuccessful();
    $response->assertSee('sort=reads');
    $response->assertSeeInOrder([
        'Article faible vue',
        'Article forte vue',
    ]);
});

test('dashboard recent comments are paginated by five and link to article comment anchor', function () {
    $user = User::factory()->create();

    $contentItem = ContentItem::factory()->internalPost()->create([
        'author_id' => $user->id,
        'status' => ContentStatus::Published->value,
        'published_at' => now()->subDay(),
    ]);

    $translation = ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Article cible commentaires',
        'slug' => 'article-cible-commentaires',
    ]);

    $latestComment = null;

    for ($i = 1; $i <= 6; $i++) {
        $comment = Comment::factory()->create([
            'content_item_id' => $contentItem->id,
            'user_id' => $user->id,
            'body_markdown' => 'Commentaire perso '.$i,
            'created_at' => now()->subMinutes($i),
        ]);

        if ($i === 1) {
            $latestComment = $comment;
        }
    }

    $response = $this->actingAs($user)->get(route('dashboard.content.index'));

    $response->assertSuccessful();
    $response->assertSee('Commentaire perso 1');
    $response->assertDontSee('Commentaire perso 6');
    $response->assertSee('comments_page=2');
    $response->assertSee(route('blog.show', [
        'locale' => $translation->locale,
        'slug' => $translation->slug,
    ]).'#comment-'.$latestComment->id, false);
});
