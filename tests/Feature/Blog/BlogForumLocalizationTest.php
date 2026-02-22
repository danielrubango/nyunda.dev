<?php

use App\Models\ForumThread;

test('blog index renders english labels for english locale', function () {
    $response = $this->withSession([
        'preferred_locale' => 'en',
    ])->get('/blog?locale=all');

    $response->assertSuccessful();
    $response->assertSee('Internal posts, external references.');
    $response->assertSee('Search');
    $response->assertSee('Monthly newsletter');
});

test('forum index redirects with coming soon flash', function () {
    $response = $this->from(route('home'))->withSession([
        'preferred_locale' => 'en',
    ])->get('/forum');

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('status', __('ui.flash.forum_coming_soon'));
});

test('forum thread page redirects with coming soon flash', function () {
    $thread = ForumThread::factory()->create([
        'slug' => 'english-thread',
        'title' => 'English thread',
    ]);

    $response = $this->from(route('home'))->withSession([
        'preferred_locale' => 'en',
    ])->get(route('forum.show', $thread));

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('status', __('ui.flash.forum_coming_soon'));
});
