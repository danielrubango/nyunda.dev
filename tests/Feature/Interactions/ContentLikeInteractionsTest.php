<?php

use App\Enums\ContentStatus;
use App\Models\ContentItem;
use App\Models\ContentLike;
use App\Models\User;

test('user can like and unlike a published internal post', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    $likeResponse = $this->actingAs($user)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]));

    $likeResponse->assertRedirect();
    expect(ContentLike::query()
        ->where('content_item_id', $contentItem->id)
        ->where('user_id', $user->id)
        ->count())->toBe(1);

    $unlikeResponse = $this->actingAs($user)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]));

    $unlikeResponse->assertRedirect();
    expect(ContentLike::query()
        ->where('content_item_id', $contentItem->id)
        ->where('user_id', $user->id)
        ->count())->toBe(0);
});

test('user cannot like non internal content', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->externalPost()->create();

    $response = $this->actingAs($user)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]));

    $response->assertForbidden();
});

test('user cannot like non published internal content', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Pending->value,
    ]);

    $response = $this->actingAs($user)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]));

    $response->assertForbidden();
});
