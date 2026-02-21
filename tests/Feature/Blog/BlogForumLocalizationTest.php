<?php

use App\Models\ForumThread;

test('blog index renders english labels for english locale', function () {
    app()->setLocale('en');

    $response = $this->get('/blog?locale=all');

    $response->assertSuccessful();
    $response->assertSee('Internal posts, external references, and community links.');
    $response->assertSee('Apply filters');
    $response->assertSee('Monthly newsletter');
});

test('forum index renders english labels for english locale', function () {
    app()->setLocale('en');

    $response = $this->get('/forum');

    $response->assertSuccessful();
    $response->assertSee('Technical discussions and community questions.');
    $response->assertSee('No discussions yet.');
});

test('forum thread page renders english reply section for english locale', function () {
    app()->setLocale('en');

    $thread = ForumThread::factory()->create([
        'slug' => 'english-thread',
        'title' => 'English thread',
    ]);

    $response = $this->get(route('forum.show', $thread));

    $response->assertSuccessful();
    $response->assertSee('Replies');
    $response->assertSee('Sign in to reply.');
});
