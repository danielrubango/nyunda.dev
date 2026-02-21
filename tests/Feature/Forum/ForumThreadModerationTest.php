<?php

use App\Models\ForumThread;
use App\Models\User;

test('admin can hide and show a forum thread', function () {
    $thread = ForumThread::factory()->create([
        'is_hidden' => false,
    ]);
    $admin = User::factory()->admin()->create();

    $hideResponse = $this->actingAs($admin)->patch(route('forum.visibility.update', $thread), [
        'is_hidden' => true,
    ]);

    $hideResponse->assertRedirect();
    $this->assertDatabaseHas('forum_threads', [
        'id' => $thread->id,
        'is_hidden' => true,
        'hidden_by_id' => $admin->id,
    ]);

    $showResponse = $this->actingAs($admin)->patch(route('forum.visibility.update', $thread), [
        'is_hidden' => false,
    ]);

    $showResponse->assertRedirect();
    $this->assertDatabaseHas('forum_threads', [
        'id' => $thread->id,
        'is_hidden' => false,
        'hidden_by_id' => null,
    ]);
});

test('non admin cannot moderate forum thread visibility', function () {
    $thread = ForumThread::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('forum.visibility.update', $thread), [
        'is_hidden' => true,
    ]);

    $response->assertForbidden();
});

test('hidden thread is not visible to guest but visible to author and admin', function () {
    $author = User::factory()->create();
    $thread = ForumThread::factory()->for($author, 'author')->create([
        'slug' => 'hidden-thread',
        'is_hidden' => true,
    ]);
    $admin = User::factory()->admin()->create();

    $this->get(route('forum.show', $thread))->assertNotFound();

    $this->actingAs($author)
        ->get(route('forum.show', $thread))
        ->assertSuccessful()
        ->assertSee($thread->title);

    $this->actingAs($admin)
        ->get(route('forum.show', $thread))
        ->assertSuccessful()
        ->assertSee($thread->title);

    $this->get(route('forum.index'))
        ->assertSuccessful()
        ->assertDontSee($thread->title);
});
