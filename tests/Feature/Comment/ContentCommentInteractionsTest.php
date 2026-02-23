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
    $response->assertSessionHas('status', __('ui.flash.comment_added'));
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

test('admin can hide a comment', function () {
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
});

test('admin can hide and unhide comment with json responses', function () {
    $admin = User::factory()->admin()->create();
    $comment = Comment::factory()->create([
        'is_hidden' => false,
    ]);

    $hideResponse = $this->actingAs($admin)
        ->withHeaders(['Accept' => 'application/json'])
        ->patch(route('comments.update', [
            'comment' => $comment,
        ]), [
            'is_hidden' => true,
        ]);

    $hideResponse->assertSuccessful();
    $hideResponse->assertJson([
        'status' => 'ok',
        'message' => __('ui.flash.comment_hidden'),
    ]);
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'is_hidden' => true,
    ]);

    $showResponse = $this->actingAs($admin)
        ->withHeaders(['Accept' => 'application/json'])
        ->patch(route('comments.update', [
            'comment' => $comment,
        ]), [
            'is_hidden' => false,
        ]);

    $showResponse->assertSuccessful();
    $showResponse->assertJson([
        'status' => 'ok',
        'message' => __('ui.flash.comment_shown'),
    ]);
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'is_hidden' => false,
    ]);
});

test('admin can unhide a hidden comment', function () {
    $admin = User::factory()->admin()->create();
    $comment = Comment::factory()->create([
        'is_hidden' => true,
        'hidden_at' => now(),
        'hidden_by_id' => $admin->id,
    ]);

    $showResponse = $this->actingAs($admin)->patch(route('comments.update', [
        'comment' => $comment,
    ]), [
        'is_hidden' => false,
    ]);

    $showResponse->assertRedirect();
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'is_hidden' => false,
        'hidden_at' => null,
        'hidden_by_id' => null,
    ]);
});

test('comment author can delete own comment', function () {
    $author = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $author->id,
    ]);

    $deleteResponse = $this->actingAs($author)->delete(route('comments.destroy', [
        'comment' => $comment,
    ]));

    $deleteResponse->assertRedirect();
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

test('admin can delete any comment', function () {
    $admin = User::factory()->admin()->create();
    $comment = Comment::factory()->create([
        'user_id' => User::factory()->create()->id,
    ]);

    $deleteResponse = $this->actingAs($admin)->delete(route('comments.destroy', [
        'comment' => $comment,
    ]));

    $deleteResponse->assertRedirect();
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

test('admin can delete any comment with json response', function () {
    $admin = User::factory()->admin()->create();
    $comment = Comment::factory()->create([
        'user_id' => User::factory()->create()->id,
    ]);

    $deleteResponse = $this->actingAs($admin)
        ->withHeaders(['Accept' => 'application/json'])
        ->delete(route('comments.destroy', [
            'comment' => $comment,
        ]));

    $deleteResponse->assertSuccessful();
    $deleteResponse->assertJson([
        'status' => 'ok',
        'message' => __('ui.flash.comment_deleted'),
    ]);
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

test('non admin users cannot hide comments or delete comments from others', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => User::factory()->create()->id,
    ]);

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

test('required comment validation message is localized in french', function () {
    app()->setLocale('fr');

    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    $response = $this->actingAs($user)->post(route('content.comments.store', [
        'contentItem' => $contentItem,
    ]), [
        'body_markdown' => '',
    ]);

    $response->assertSessionHasErrors([
        'body_markdown' => __('ui.blog.comments.validation_required'),
    ]);
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
