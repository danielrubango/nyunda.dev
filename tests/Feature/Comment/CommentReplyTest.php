<?php

use App\Actions\Comments\AddCommentToContentItem;
use App\Models\Comment;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\User;

test('user can post a top-level comment', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->commentsEnabled()->create();

    $action = app(AddCommentToContentItem::class);
    $comment = $action->handle($user, $contentItem, 'Mon commentaire de test.');

    expect($comment)->not->toBeNull();
    expect($comment->content_item_id)->toBe($contentItem->id);
    expect($comment->user_id)->toBe($user->id);
    expect($comment->parent_id)->toBeNull();
    expect($comment->body_markdown)->toBe('Mon commentaire de test.');
});

test('user can post a reply to an existing comment', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->commentsEnabled()->create();
    $parentComment = Comment::factory()->create([
        'content_item_id' => $contentItem->id,
        'parent_id' => null,
    ]);

    $action = app(AddCommentToContentItem::class);
    $reply = $action->handle($user, $contentItem, 'Ma réponse.', $parentComment->id);

    expect($reply->parent_id)->toBe($parentComment->id);
    expect($reply->body_markdown)->toBe('Ma réponse.');
});

test('comment reply relationship works correctly', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->commentsEnabled()->create();
    $parent = Comment::factory()->create(['content_item_id' => $contentItem->id, 'parent_id' => null]);
    $reply1 = Comment::factory()->create(['content_item_id' => $contentItem->id, 'parent_id' => $parent->id]);
    $reply2 = Comment::factory()->create(['content_item_id' => $contentItem->id, 'parent_id' => $parent->id]);

    expect($parent->isReply())->toBeFalse();
    expect($parent->replies()->count())->toBe(2);
    expect($reply1->isReply())->toBeTrue();
    expect($reply1->parent->id)->toBe($parent->id);
});

test('store comment via http supports parent_id', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->commentsEnabled()->create();
    $translation = ContentTranslation::factory()->for($contentItem, 'contentItem')->create();
    $parent = Comment::factory()->create(['content_item_id' => $contentItem->id, 'parent_id' => null]);

    $this->actingAs($user)
        ->post(route('content.comments.store', $contentItem), [
            'body_markdown' => 'Une réponse via HTTP.',
            'parent_id' => $parent->id,
        ])
        ->assertRedirect();

    expect(
        Comment::where('content_item_id', $contentItem->id)
            ->where('user_id', $user->id)
            ->where('parent_id', $parent->id)
            ->where('body_markdown', 'Une réponse via HTTP.')
            ->exists()
    )->toBeTrue();
});
