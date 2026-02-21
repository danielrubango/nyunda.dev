<?php

namespace App\Livewire\Public;

use App\Actions\Comments\ToggleContentLike;
use App\Models\ContentItem;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class ContentLikeButton extends Component
{
    public ContentItem $contentItem;

    public int $likesCount = 0;

    public bool $liked = false;

    public function mount(ContentItem $contentItem, bool $liked = false): void
    {
        $this->contentItem = $contentItem;
        $this->liked = $liked;
        $this->likesCount = (int) ($contentItem->likes_count ?? $contentItem->likes()->count());
    }

    public function toggleLike(): void
    {
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        Gate::authorize('like', $this->contentItem);

        $this->liked = app(ToggleContentLike::class)->handle(
            user: $user,
            contentItem: $this->contentItem,
        );

        $this->likesCount = (int) $this->contentItem->likes()->count();
        $message = $this->liked
            ? __('ui.flash.like_added')
            : __('ui.flash.like_removed');

        $this->dispatch('ui-toast', message: $message, variant: 'success');
    }

    public function render(): View
    {
        return view('livewire.public.content-like-button');
    }
}
