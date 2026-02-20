<?php

use App\Enums\ContentStatus;
use App\Models\Comment;
use App\Models\ContentItem;
use App\Models\User;

test('authenticated user can comment on published internal content', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    $response = $this->actingAs($user)->post(route('content.comments.store', [
        'contentItem' => $contentItem,
    ]), [
        'body_markdown' => 'This is a valid comment.',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('comments', [
        'content_item_id' => $contentItem->id,
        'user_id' => $user->id,
        'is_hidden' => false,
    ]);
});

test('user cannot comment on external content', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->externalPost()->create();

    $response = $this->actingAs($user)->post(route('content.comments.store', [
        'contentItem' => $contentItem,
    ]), [
        'body_markdown' => 'Blocked comment.',
    ]);

    $response->assertForbidden();
});

test('admin can hide and delete a comment', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $author->id,
    ]);

    $hideResponse = $this->actingAs($admin)->patch(route('comments.update', [
        'comment' => $comment,
    ]), [
        'is_hidden' => true,
    ]);

    $hideResponse->assertRedirect();
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'is_hidden' => true,
        'hidden_by_id' => $admin->id,
    ]);

    $deleteResponse = $this->actingAs($admin)->delete(route('comments.destroy', [
        'comment' => $comment,
    ]));

    $deleteResponse->assertRedirect();
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

test('non admin users cannot hide or delete comments', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create();

    $hideResponse = $this->actingAs($user)->patch(route('comments.update', [
        'comment' => $comment,
    ]), [
        'is_hidden' => true,
    ]);
    $deleteResponse = $this->actingAs($user)->delete(route('comments.destroy', [
        'comment' => $comment,
    ]));

    $hideResponse->assertForbidden();
    $deleteResponse->assertForbidden();
});

test('comment payload must respect validation rules', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create();
    $pendingItem = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Pending->value,
    ]);

    $invalidBodyResponse = $this->actingAs($user)->post(route('content.comments.store', [
        'contentItem' => $contentItem,
    ]), [
        'body_markdown' => 'a',
    ]);

    $invalidBodyResponse->assertSessionHasErrors(['body_markdown']);

    $pendingResponse = $this->actingAs($user)->post(route('content.comments.store', [
        'contentItem' => $pendingItem,
    ]), [
        'body_markdown' => 'Valid length comment.',
    ]);

    $pendingResponse->assertForbidden();
});

test('user cannot comment when comments are disabled on internal post', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'show_comments' => false,
    ]);

    $response = $this->actingAs($user)->post(route('content.comments.store', [
        'contentItem' => $contentItem,
    ]), [
        'body_markdown' => 'This should not be accepted.',
    ]);

    $response->assertForbidden();
});
