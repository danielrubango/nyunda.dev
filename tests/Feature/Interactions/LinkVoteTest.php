<?php

use App\Enums\ContentType;
use App\Livewire\Public\LinkVoteButton;
use App\Models\ContentItem;
use App\Models\User;
use Livewire\Livewire;

test('authenticated user can toggle vote on a community link', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->externalPost()->create();

    Livewire::actingAs($user)
        ->test(LinkVoteButton::class, [
            'contentItem' => $contentItem,
            'voted' => false,
        ])
        ->assertSet('votesCount', 0)
        ->call('toggleVote')
        ->assertSet('voted', true)
        ->assertSet('votesCount', 1)
        ->assertDispatched('ui-toast', message: __('ui.flash.vote_added'), variant: 'success')
        ->call('toggleVote')
        ->assertSet('voted', false)
        ->assertSet('votesCount', 0)
        ->assertDispatched('ui-toast', message: __('ui.flash.vote_removed'), variant: 'success');

    expect(\App\Models\LinkVote::where('content_item_id', $contentItem->id)->where('user_id', $user->id)->exists())->toBeFalse();
});

test('guest receives login_required toast when trying to vote', function () {
    $contentItem = ContentItem::factory()->published()->externalPost()->create();

    Livewire::test(LinkVoteButton::class, [
        'contentItem' => $contentItem,
        'voted' => false,
    ])
        ->assertSet('votesCount', 0)
        ->call('toggleVote')
        ->assertSet('voted', false)
        ->assertSet('votesCount', 0)
        ->assertDispatched('ui-toast', message: __('ui.flash.login_required'), variant: 'warning');
});

test('vote is unique per user per content item', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->externalPost()->create();

    // Premier vote
    Livewire::actingAs($user)
        ->test(LinkVoteButton::class, ['contentItem' => $contentItem])
        ->call('toggleVote')
        ->assertSet('voted', true);

    expect(\App\Models\LinkVote::count())->toBe(1);

    // Double vote ne crée pas de doublon
    Livewire::actingAs($user)
        ->test(LinkVoteButton::class, ['contentItem' => $contentItem, 'voted' => true])
        ->call('toggleVote')
        ->assertSet('voted', false);

    expect(\App\Models\LinkVote::count())->toBe(0);
});

