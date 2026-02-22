<?php

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;

beforeEach(function (): void {
    $this->markTestSkipped('Forum routes are temporarily disabled.');
});

test('authenticated user can reply to a forum thread', function () {
    $thread = ForumThread::factory()->create([
        'slug' => 'thread-reply',
    ]);
    $responder = User::factory()->create();

    $response = $this->actingAs($responder)->post(route('forum.replies.store', $thread), [
        'body_markdown' => 'Tu peux commencer par superviser les jobs lents avec des tags.',
    ]);

    $response->assertRedirect(route('forum.show', $thread).'#replies');

    $this->assertDatabaseHas('forum_replies', [
        'forum_thread_id' => $thread->id,
        'user_id' => $responder->id,
    ]);
});

test('thread author can mark a reply as best reply', function () {
    $author = User::factory()->create();
    $thread = ForumThread::factory()->for($author, 'author')->create([
        'slug' => 'best-reply-thread',
    ]);
    $reply = ForumReply::factory()->for($thread)->create();

    $response = $this->actingAs($author)->post(route('forum.replies.mark-best', [
        'forumThread' => $thread,
        'forumReply' => $reply,
    ]));

    $response->assertRedirect();
    $this->assertDatabaseHas('forum_threads', [
        'id' => $thread->id,
        'best_reply_id' => $reply->id,
    ]);
});

test('regular user cannot mark a reply as best reply', function () {
    $author = User::factory()->create();
    $thread = ForumThread::factory()->for($author, 'author')->create();
    $reply = ForumReply::factory()->for($thread)->create();
    $randomUser = User::factory()->create();

    $response = $this->actingAs($randomUser)->post(route('forum.replies.mark-best', [
        'forumThread' => $thread,
        'forumReply' => $reply,
    ]));

    $response->assertForbidden();
    $this->assertDatabaseHas('forum_threads', [
        'id' => $thread->id,
        'best_reply_id' => null,
    ]);
});

test('admin can hide a forum reply', function () {
    $thread = ForumThread::factory()->create();
    $reply = ForumReply::factory()->for($thread)->create([
        'is_hidden' => false,
    ]);
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->patch(route('forum.replies.update', [
        'forumReply' => $reply,
    ]), [
        'is_hidden' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('forum_replies', [
        'id' => $reply->id,
        'is_hidden' => true,
        'hidden_by_id' => $admin->id,
    ]);
});
