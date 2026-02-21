<?php

use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\ForumThread;
use App\Models\Tag;
use App\Models\User;

test('home page renders key public sections', function () {
    $internal = ContentItem::factory()->published()->internalPost()->create();
    $external = ContentItem::factory()->published()->externalPost()->create();
    $community = ContentItem::factory()->published()->communityLink()->create();

    ContentTranslation::factory()->for($internal)->forLocale('fr')->create([
        'title' => 'Article interne',
        'excerpt' => 'Extrait interne',
        'slug' => 'article-interne-home',
    ]);
    ContentTranslation::factory()->for($external)->forLocale('fr')->create([
        'title' => 'Article externe',
        'excerpt' => 'Extrait externe',
        'external_url' => 'https://example.com/external-home',
        'slug' => 'article-externe-home',
    ]);
    ContentTranslation::factory()->for($community)->forLocale('fr')->create([
        'title' => 'Lien communauté',
        'excerpt' => 'Extrait communauté',
        'external_url' => 'https://example.com/community-home',
        'slug' => 'lien-communaute-home',
    ]);

    $response = $this->withHeaders([
        'Accept-Language' => 'fr',
    ])->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('Article en vedette');
    $response->assertSee('Articles populaires');
    $response->assertSee('Liens récents');
    $response->assertSee('Article interne');
    $response->assertSee('Article externe');
    $response->assertSee('Mon compte');
});

test('links page lists only external and community links', function () {
    $external = ContentItem::factory()->published()->externalPost()->create();
    $community = ContentItem::factory()->published()->communityLink()->create();
    $internal = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($external)->forLocale('fr')->create([
        'title' => 'External resource',
        'external_url' => 'https://example.com/external-link',
        'slug' => 'external-resource-link',
    ]);
    ContentTranslation::factory()->for($community)->forLocale('fr')->create([
        'title' => 'Community resource',
        'external_url' => 'https://www.community.dev/community-link',
        'external_og_image_url' => 'https://cdn.community.dev/og.png',
        'slug' => 'community-resource-link',
    ]);
    ContentTranslation::factory()->for($internal)->forLocale('fr')->create([
        'title' => 'Internal resource',
        'slug' => 'internal-resource-link',
    ]);

    $response = $this->withHeaders([
        'Accept-Language' => 'fr',
    ])->get(route('links.index'));

    $response->assertSuccessful();
    $response->assertSee('External resource');
    $response->assertSee('Community resource');
    $response->assertDontSee('Internal resource');
    $response->assertSee('data-testid="community-link-card"', false);
    $response->assertSee('data-layout="full-width"', false);
    $response->assertSee('href="https://www.community.dev/community-link"', false);
    $response->assertDontSee('cdn.community.dev/og.png');
    $response->assertSee('Partagé le '.$community->published_at?->format('Y-m-d').' — community.dev');
    $response->assertSee('data-testid="external-link-card"', false);

    expect(substr_count((string) $response->getContent(), 'data-layout="full-width"'))->toBe(1);
});

test('blog index supports tag filter and paginated query string', function () {
    $tagLaravel = Tag::factory()->create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);
    $tagPhp = Tag::factory()->create([
        'name' => 'PHP',
        'slug' => 'php',
    ]);

    foreach (range(1, 13) as $index) {
        $item = ContentItem::factory()->published()->internalPost()->create();
        $item->tags()->sync([$tagLaravel->id]);

        ContentTranslation::factory()->for($item)->forLocale('fr')->create([
            'title' => "Laravel Post {$index}",
            'slug' => "laravel-post-{$index}",
        ]);
    }

    $phpOnlyItem = ContentItem::factory()->published()->internalPost()->create();
    $phpOnlyItem->tags()->sync([$tagPhp->id]);
    ContentTranslation::factory()->for($phpOnlyItem)->forLocale('fr')->create([
        'title' => 'PHP only post',
        'slug' => 'php-only-post',
    ]);

    $response = $this->get(route('blog.index', [
        'tag' => 'laravel',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Laravel Post 1');
    $response->assertDontSee('PHP only post');
    $response->assertSee('tag=laravel', false);
});

test('blog localized slug route redirects to locale specific route', function () {
    $item = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($item)->forLocale('fr')->create([
        'slug' => 'redirection-slug',
        'title' => 'Redirection slug',
    ]);

    $response = $this->get(route('blog.show.localized', ['slug' => 'redirection-slug']));

    $response->assertRedirect(route('blog.show', [
        'locale' => 'fr',
        'slug' => 'redirection-slug',
    ]));
});

test('forum shows coming soon page and blocks forum routes when disabled', function () {
    config()->set('features.forum_enabled', false);

    $thread = ForumThread::factory()->create([
        'locale' => 'fr',
        'title' => 'Thread FR',
        'slug' => 'thread-fr',
    ]);

    $response = $this->get(route('forum.index'));

    $response->assertSuccessful();
    $response->assertSee('Soon');
    $response->assertSee('Forum coming soon');

    $this->get(route('forum.show', $thread))->assertNotFound();

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('forum.create'))
        ->assertNotFound();
});

test('selected locale persists across public pages', function () {
    $this->withHeaders([
        'Accept-Language' => 'en',
    ])->from(route('home'))
        ->post(route('locale.update'), [
            'locale' => 'fr',
        ])
        ->assertRedirect(route('home'));

    $this->withHeaders([
        'Accept-Language' => 'en',
    ])->get(route('about.show'))
        ->assertSuccessful()
        ->assertSee('À propos');

    $this->withHeaders([
        'Accept-Language' => 'en',
    ])->get(route('links.index'))
        ->assertSuccessful()
        ->assertSee('Liens utiles');
});

test('links page supports search filter', function () {
    $firstItem = ContentItem::factory()->published()->externalPost()->create();
    $secondItem = ContentItem::factory()->published()->communityLink()->create();

    ContentTranslation::factory()->for($firstItem)->forLocale('fr')->create([
        'title' => 'Laravel Queues Guide',
        'external_url' => 'https://example.com/laravel-queues',
        'slug' => 'laravel-queues-guide',
    ]);

    ContentTranslation::factory()->for($secondItem)->forLocale('fr')->create([
        'title' => 'Rust Performance Notes',
        'external_url' => 'https://example.com/rust-performance',
        'slug' => 'rust-performance-notes',
    ]);

    $response = $this->get(route('links.index', [
        'q' => 'laravel',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Laravel Queues Guide');
    $response->assertDontSee('Rust Performance Notes');
});
