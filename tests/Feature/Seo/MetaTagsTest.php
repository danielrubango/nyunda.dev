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
});

test('blog detail renders article metadata', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();
    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'title' => 'Meta article',
        'slug' => 'meta-article',
        'excerpt' => 'Meta excerpt',
    ]);

    $response = $this->get('/blog/fr/meta-article');

    $response->assertSuccessful();
    $response->assertSee('<meta property="og:type" content="article">', false);
    $response->assertSee('<meta name="description" content="Meta excerpt">', false);
    $response->assertSee('<meta name="twitter:title" content="Meta article | '.config('app.name').'">', false);
});

test('public profile page renders canonical metadata', function () {
    $user = User::factory()->withPublicProfile('meta-user')->create([
        'name' => 'Meta User',
    ]);

    $response = $this->get('/u/'.$user->public_profile_slug);

    $response->assertSuccessful();
    $response->assertSee('<link rel="canonical" href="'.route('profiles.show', ['username' => 'meta-user']).'">', false);
    $response->assertSee('<meta property="og:title" content="Meta User | '.config('app.name').'">', false);
});

test('google analytics snippet is rendered when measurement id is configured', function () {
    config()->set('services.analytics.google.measurement_id', 'G-TEST1234');

    $response = $this->get('/blog');

    $response->assertSuccessful();
    $response->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST1234', false);
    $response->assertSee("gtag('config', 'G-TEST1234');", false);
});
