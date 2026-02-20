<?php

namespace App\Actions\Forum;

use App\Models\ForumReply;
use App\Models\ForumThread;
use InvalidArgumentException;

class MarkBestForumReply
{
    public function handle(ForumThread $forumThread, ForumReply $forumReply): ForumThread
    {
        if ($forumReply->forum_thread_id !== $forumThread->id) {
            throw new InvalidArgumentException('The reply does not belong to the selected thread.');
        }

        $forumThread->update([
            'best_reply_id' => $forumReply->id,
        ]);

        return $forumThread->refresh();
    }
}
