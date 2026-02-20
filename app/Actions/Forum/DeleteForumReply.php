<?php

namespace App\Actions\Forum;

use App\Models\ForumReply;

class DeleteForumReply
{
    public function handle(ForumReply $forumReply): void
    {
        $forumReply->loadMissing('forumThread');

        if ($forumReply->forumThread !== null && $forumReply->forumThread->best_reply_id === $forumReply->id) {
            $forumReply->forumThread->update([
                'best_reply_id' => null,
            ]);
        }

        $forumReply->delete();
    }
}
