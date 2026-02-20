<?php

namespace App\Http\Controllers\Forum;

use App\Actions\Forum\AddForumReply;
use App\Actions\Forum\DeleteForumReply;
use App\Actions\Forum\UpdateForumReplyVisibility;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreForumReplyRequest;
use App\Http\Requests\Forum\UpdateForumReplyRequest;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ForumRepliesController extends Controller
{
    public function store(
        StoreForumReplyRequest $request,
        ForumThread $forumThread,
        AddForumReply $addForumReply,
    ): RedirectResponse {
        $this->authorize('create', ForumReply::class);

        if ($forumThread->is_hidden && ! $this->canAccessHiddenThread($request->user(), $forumThread)) {
            abort(404);
        }

        $addForumReply->handle(
            user: $request->user(),
            forumThread: $forumThread,
            bodyMarkdown: $request->bodyMarkdown(),
        );

        return redirect()->route('forum.show', $forumThread)->withFragment('replies');
    }

    public function update(
        UpdateForumReplyRequest $request,
        ForumReply $forumReply,
        UpdateForumReplyVisibility $updateForumReplyVisibility,
    ): RedirectResponse {
        $this->authorize('moderate', $forumReply);

        $updateForumReplyVisibility->handle(
            forumReply: $forumReply,
            isHidden: $request->isHidden(),
            hiddenById: $request->user()->id,
        );

        return redirect()->back();
    }

    public function destroy(ForumReply $forumReply, DeleteForumReply $deleteForumReply): RedirectResponse
    {
        $this->authorize('delete', $forumReply);

        $deleteForumReply->handle($forumReply);

        return redirect()->back();
    }

    protected function canAccessHiddenThread(User $user, ForumThread $forumThread): bool
    {
        return $user->hasRole(UserRole::Admin) || $forumThread->author_id === $user->id;
    }
}
