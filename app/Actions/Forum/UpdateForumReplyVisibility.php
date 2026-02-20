<?php

namespace App\Actions\Forum;

use App\Models\ForumReply;

class UpdateForumReplyVisibility
{
    public function handle(ForumReply $forumReply, bool $isHidden, int $hiddenById): ForumReply
    {
        $forumReply->loadMissing('forumThread');

        $forumReply->update([
            'is_hidden' => $isHidden,
            'hidden_at' => $isHidden ? now() : null,
            'hidden_by_id' => $isHidden ? $hiddenById : null,
        ]);

        if ($isHidden && $forumReply->forumThread !== null && $forumReply->forumThread->best_reply_id === $forumReply->id) {
            $forumReply->forumThread->update([
                'best_reply_id' => null,
            ]);
        }

        return $forumReply->refresh();
    }
}
