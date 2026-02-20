<?php

namespace App\Actions\Forum;

use App\Models\ForumThread;

class DeleteForumThread
{
    public function handle(ForumThread $forumThread): void
    {
        $forumThread->delete();
    }
}
