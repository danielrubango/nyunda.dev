<?php

use App\Livewire\Public\ContentLikeButton;
use App\Models\ContentItem;
use App\Models\User;
use Livewire\Livewire;

test('authenticated user can toggle like state from livewire like button', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create();
    $contentItem->loadCount('likes');

    Livewire::actingAs($user)
        ->test(ContentLikeButton::class, [
            'contentItem' => $contentItem,
            'liked' => false,
        ])
        ->assertSet('likesCount', 0)
        ->call('toggleLike')
        ->assertSet('liked', true)
        ->assertSet('likesCount', 1)
        ->assertDispatched('ui-toast', message: __('ui.flash.like_added'), variant: 'success')
        ->call('toggleLike')
        ->assertSet('liked', false)
        ->assertSet('likesCount', 0)
        ->assertDispatched('ui-toast', message: __('ui.flash.like_removed'), variant: 'success');

    $this->assertDatabaseMissing('content_likes', [
        'content_item_id' => $contentItem->id,
        'user_id' => $user->id,
    ]);
});

test('guest cannot toggle like state from livewire like button', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();
    $contentItem->loadCount('likes');

    Livewire::test(ContentLikeButton::class, [
        'contentItem' => $contentItem,
        'liked' => false,
    ])
        ->assertSet('likesCount', 0)
        ->call('toggleLike')
        ->assertSet('liked', false)
        ->assertSet('likesCount', 0);
});
