<?php

use App\Models\ForumThread;
use App\Models\User;

beforeEach(function (): void {
    $this->markTestSkipped('Forum routes are temporarily disabled.');
});

test('guest cannot create forum thread', function () {
    $response = $this->get('/forum/create');

    $response->assertRedirect('/login');
});

test('authenticated user can create forum thread', function () {
    $user = User::factory()->create([
        'preferred_locale' => 'fr',
    ]);

    $response = $this->actingAs($user)->post('/forum', [
        'locale' => 'fr',
        'title' => 'Comment optimiser Laravel Horizon en production ?',
        'body_markdown' => 'Je cherche une stratégie pragmatique pour surveiller et régler les workers.',
    ]);

    $thread = ForumThread::query()->first();

    expect($thread)->not->toBeNull();

    $response->assertRedirect(route('forum.show', $thread));

    $this->assertDatabaseHas('forum_threads', [
        'author_id' => $user->id,
        'locale' => 'fr',
        'title' => 'Comment optimiser Laravel Horizon en production ?',
    ]);
});

test('forum index lists published discussions', function () {
    $thread = ForumThread::factory()->create([
        'title' => 'Discussion architecture',
        'slug' => 'discussion-architecture',
    ]);

    $response = $this->get('/forum');

    $response->assertSuccessful();
    $response->assertSee('Discussion architecture');
    $response->assertSee(route('forum.show', $thread));
});
