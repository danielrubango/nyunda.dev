<?php

namespace App\Http\Controllers\Forum;

use App\Actions\Forum\UpdateForumThreadVisibility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\UpdateForumThreadVisibilityRequest;
use App\Models\ForumThread;
use Illuminate\Http\RedirectResponse;

class UpdateForumThreadVisibilityController extends Controller
{
    public function __invoke(
        UpdateForumThreadVisibilityRequest $request,
        ForumThread $forumThread,
        UpdateForumThreadVisibility $updateForumThreadVisibility,
    ): RedirectResponse {
        $preferredLocale = $request->user()?->preferred_locale;

        if (is_string($preferredLocale) && in_array($preferredLocale, config('app.supported_locales', ['fr', 'en']), true)) {
            app()->setLocale($preferredLocale);
        }

        $this->authorize('moderate', $forumThread);

        $updateForumThreadVisibility->handle(
            forumThread: $forumThread,
            isHidden: $request->isHidden(),
            hiddenById: $request->user()->id,
        );

        return redirect()->back()->with('status', $request->isHidden()
            ? __('ui.forum.status.hidden')
            : __('ui.forum.status.shown'));
    }
}
