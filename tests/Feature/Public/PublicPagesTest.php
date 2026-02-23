<?php

use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\Tag;
use App\Models\User;

test('home page renders key public sections', function () {
    $internal = ContentItem::factory()->published()->internalPost()->create();
    $external = ContentItem::factory()->published()->externalPost()->create();
    $legacyCommunity = ContentItem::factory()->published()->create([
        'type' => ContentType::CommunityLink->value,
    ]);

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
    ContentTranslation::factory()->for($legacyCommunity)->forLocale('fr')->create([
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
    $response->assertDontSee('Lien communauté');
    $response->assertSee('Mon compte');
    $response->assertSee('id="footer-locale-select"', false);
});

test('authenticated non admin user sees account dropdown with settings and logout only', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(__('ui.nav.account'));
    $response->assertSee(__('ui.nav.settings'));
    $response->assertSee(__('ui.nav.logout'));
    $response->assertDontSee('href="'.route('dashboard').'"', false);
    $response->assertSee($user->initials());
    $response->assertSee('rounded-full border border-zinc-300', false);
});

test('authenticated admin user sees account dropdown with dashboard settings and logout', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(__('ui.nav.account'));
    $response->assertSee(__('ui.nav.settings'));
    $response->assertSee(__('ui.nav.logout'));
    $response->assertSee('href="'.route('dashboard').'"', false);
    $response->assertSee($admin->initials());
    $response->assertSee('rounded-full border border-zinc-300', false);
});

test('links page lists only external links', function () {
    $external = ContentItem::factory()->published()->externalPost()->create();
    $community = ContentItem::factory()->published()->create([
        'type' => ContentType::CommunityLink->value,
    ]);
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
    $response->assertDontSee('Community resource');
    $response->assertDontSee('Internal resource');
    $response->assertSee('data-testid="external-link-card"', false);
    $response->assertSee('class="pr-8 font-sans text-xl font-semibold tracking-tight text-zinc-900 transition-colors group-hover:text-brand-700"', false);
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

test('forum routes redirect back with a coming soon flash', function () {
    $fromHomeResponse = $this
        ->from(route('home'))
        ->get(route('forum.index'));

    $fromHomeResponse->assertRedirect(route('home'));
    $fromHomeResponse->assertSessionHas('status', __('ui.flash.forum_coming_soon'));

    $directResponse = $this->get(route('forum.index'));

    $directResponse->assertRedirect(route('home'));
    $directResponse->assertSessionHas('status', __('ui.flash.forum_coming_soon'));
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

test('public layout renders toast container with flashed session message', function () {
    $response = $this->withSession([
        'status' => 'Toast test message',
    ])->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('id="ui-toast-root"', false);
    $response->assertSee('Toast test message');
});

test('links page supports search filter', function () {
    $firstItem = ContentItem::factory()->published()->externalPost()->create();
    $secondItem = ContentItem::factory()->published()->externalPost()->create();

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
