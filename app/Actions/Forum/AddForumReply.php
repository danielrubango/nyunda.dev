<?php

namespace App\Actions\Forum;

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;

class AddForumReply
{
    public function handle(User $user, ForumThread $forumThread, string $bodyMarkdown): ForumReply
    {
        return ForumReply::query()->create([
            'forum_thread_id' => $forumThread->id,
            'user_id' => $user->id,
            'body_markdown' => $bodyMarkdown,
        ]);
    }
}
