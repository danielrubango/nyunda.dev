<?php

use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\User;

test('blog listing renders canonical and open graph metadata', function () {
    $response = $this->get('/blog');

    $response->assertSuccessful();
    $response->assertSee('<link rel="canonical" href="'.route('blog.index').'">', false);
    $response->assertSee('<meta property="og:type" content="website">', false);
    $response->assertSee('<meta name="twitter:card" content="summary">', false);
    $response->assertSee('<meta name="description" content="'.e(__('ui.seo.meta.blog')).'">', false);
});

test('public pages render targeted seo descriptions', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('<meta name="description" content="publie des articles concrets sur Laravel, PHP et l&#039;IA, avec des ressources utiles pour les developpeurs et les equipes produit.">', false)
        ->assertSee('application/ld+json', false)
        ->assertSee('"@type": "WebSite"', false);

    $this->get('/about')
        ->assertSuccessful()
        ->assertSee('<meta name="description" content="'.e(__('ui.seo.meta.about')).'">', false);

    $this->get('/links')
        ->assertSuccessful()
        ->assertSee('<meta name="description" content="'.e(__('ui.seo.meta.links')).'">', false);
});

test('blog detail renders article metadata with alternates and schema', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();
    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Meta article',
        'slug' => 'meta-article',
        'excerpt' => 'Court extrait',
        'body_markdown' => "# Meta article\n\nThis article explains how to improve SEO metadata in Laravel without letting snippets drift away from the actual topic.",
    ]);
    ContentTranslation::factory()->for($contentItem)->forLocale('en')->create([
        'title' => 'Meta article',
        'slug' => 'meta-article-en',
        'excerpt' => 'Tiny excerpt',
        'body_markdown' => "# Meta article\n\nThis article explains how to improve SEO metadata in Laravel without letting snippets drift away from the actual topic.",
    ]);

    $response = $this->get('/blog/fr/meta-article');

    $response->assertSuccessful();
    $response->assertSee('<meta property="og:type" content="article">', false);
    $response->assertDontSee('<meta name="description" content="Court extrait">', false);
    $response->assertSee('This article explains how to improve SEO metadata in Laravel without letting snippets drift away from the actual topic', false);
    $response->assertSee('<meta name="twitter:title" content="Meta article | '.config('app.name').'">', false);
    $response->assertSee('<link rel="alternate" hreflang="fr" href="'.route('blog.show', ['locale' => 'fr', 'slug' => 'meta-article']).'">', false);
    $response->assertSee('<link rel="alternate" hreflang="en" href="'.route('blog.show', ['locale' => 'en', 'slug' => 'meta-article-en']).'">', false);
    $response->assertSee('<link rel="alternate" hreflang="x-default" href="'.route('blog.show', ['locale' => 'fr', 'slug' => 'meta-article']).'">', false);
    $response->assertSee('application/ld+json', false);
    $response->assertSee('"@type": "Article"', false);
    $response->assertSee('article:published_time', false);
    $response->assertSee('article:modified_time', false);
});

test('public profile page renders canonical metadata', function () {
    $user = User::factory()->withPublicProfile('meta-user')->create([
        'name' => 'Meta User',
        'headline' => 'Laravel engineer',
        'bio' => 'Public profile for a Laravel engineer who writes practical notes about backend architecture.',
    ]);

    $response = $this->get('/u/'.$user->public_profile_slug);

    $response->assertSuccessful();
    $response->assertSee('<link rel="canonical" href="'.route('profiles.show', ['username' => 'meta-user']).'">', false);
    $response->assertSee('<meta property="og:title" content="Meta User | '.config('app.name').'">', false);
    $response->assertSee('application/ld+json', false);
    $response->assertSee('"@type": "ProfilePage"', false);
    $response->assertSee('Laravel engineer', false);
});

test('google analytics snippet is rendered when measurement id is configured', function () {
    config()->set('services.analytics.google.measurement_id', 'G-TEST1234');
    config()->set('services.analytics.google.stream_id', '1234567890');
    config()->set('services.analytics.google.api_secret', 'secret-should-not-leak');
    config()->set('services.analytics.google.tag_manager_id', 'GTM-N7KBDBQ');

    $response = $this->get('/blog');

    $response->assertSuccessful();
    $response->assertSee("})(window,document,'script','dataLayer','GTM-N7KBDBQ');", false);
    $response->assertSee('https://www.googletagmanager.com/ns.html?id=GTM-N7KBDBQ', false);
    $response->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST1234', false);
    $response->assertSee("gtag('config', 'G-TEST1234');", false);
    $response->assertSee('window.nyundaAnalytics = {', false);
    $response->assertSee("measurementId: 'G-TEST1234'", false);
    $response->assertSee("streamId: '1234567890'", false);
    $response->assertDontSee('secret-should-not-leak', false);
});
