<?php

namespace App\Livewire\Public;

use App\Actions\Content\ToggleLinkVote;
use App\Models\ContentItem;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class LinkVoteButton extends Component
{
    public ContentItem $contentItem;

    public int $votesCount = 0;

    public bool $voted = false;

    public function mount(ContentItem $contentItem, bool $voted = false): void
    {
        $this->contentItem = $contentItem;
        $this->voted = $voted;
        $this->votesCount = (int) ($contentItem->votes_count ?? $contentItem->linkVotes()->count());
    }

    public function toggleVote(): void
    {
        /** @var Guard $auth */
        $auth = auth();

        if (! $auth->check()) {
            $this->dispatch('ui-toast', message: __('ui.flash.login_required'), variant: 'warning');

            return;
        }

        $user = $auth->user();

        if (! $user instanceof User) {
            return;
        }

        Gate::authorize('vote', $this->contentItem);

        $this->voted = app(ToggleLinkVote::class)->handle(
            user: $user,
            contentItem: $this->contentItem,
        );

        $this->votesCount = (int) $this->contentItem->linkVotes()->count();

        $message = $this->voted
            ? __('ui.flash.vote_added')
            : __('ui.flash.vote_removed');

        $this->dispatch('ui-toast', message: $message, variant: 'success');
    }

    public function render(): View
    {
        return view('livewire.public.link-vote-button');
    }
}
