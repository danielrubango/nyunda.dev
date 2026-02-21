<?php

namespace App\Http\Controllers\Forum;

use App\Actions\Forum\MarkBestForumReply;
use App\Http\Controllers\Controller;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\RedirectResponse;

class MarkBestForumReplyController extends Controller
{
    public function __invoke(
        ForumThread $forumThread,
        ForumReply $forumReply,
        MarkBestForumReply $markBestForumReply,
    ): RedirectResponse {
        $preferredLocale = auth()->user()?->preferred_locale;

        if (is_string($preferredLocale) && in_array($preferredLocale, config('app.supported_locales', ['fr', 'en']), true)) {
            app()->setLocale($preferredLocale);
        }

        $this->authorize('markBestReply', $forumThread);

        abort_if($forumReply->forum_thread_id !== $forumThread->id, 404);

        $markBestForumReply->handle($forumThread, $forumReply);

        return redirect()->back()->with('status', __('ui.forum.status.best_reply_marked'));
    }
}
