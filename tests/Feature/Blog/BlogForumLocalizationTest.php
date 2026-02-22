<?php

use App\Models\ForumThread;

test('blog index renders english labels for english locale', function () {
    $response = $this->withSession([
        'preferred_locale' => 'en',
    ])->get('/blog?locale=all');

    $response->assertSuccessful();
    $response->assertSee('Internal posts, external references, and community links.');
    $response->assertSee('Search');
    $response->assertSee('Monthly newsletter');
});

test('forum index renders english labels for english locale', function () {
    $response = $this->withSession([
        'preferred_locale' => 'en',
    ])->get('/forum');

    $response->assertSuccessful();
    $response->assertSee('Technical discussions and community questions.');
    $response->assertSee('No discussions yet.');
});

test('forum thread page renders english reply section for english locale', function () {
    $thread = ForumThread::factory()->create([
        'slug' => 'english-thread',
        'title' => 'English thread',
    ]);

    $response = $this->withSession([
        'preferred_locale' => 'en',
    ])->get(route('forum.show', $thread));

    $response->assertSuccessful();
    $response->assertSee('Replies');
    $response->assertSee('Sign in to reply.');
});
