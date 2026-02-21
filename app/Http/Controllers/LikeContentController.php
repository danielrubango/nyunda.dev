<?php

namespace App\Http\Controllers;

use App\Actions\Comments\ToggleContentLike;
use App\Models\ContentItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LikeContentController extends Controller
{
    public function __invoke(
        Request $request,
        ContentItem $contentItem,
        ToggleContentLike $toggleContentLike,
    ): RedirectResponse {
        $this->authorize('like', $contentItem);

        $hasLiked = $toggleContentLike->handle(
            user: $request->user(),
            contentItem: $contentItem,
        );

        return redirect()
            ->back()
            ->with('status', $hasLiked ? __('ui.flash.like_added') : __('ui.flash.like_removed'));
    }
}
