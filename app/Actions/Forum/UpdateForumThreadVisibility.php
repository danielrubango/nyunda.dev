<?php

namespace App\Actions\Forum;

use App\Models\ForumThread;

class UpdateForumThreadVisibility
{
    public function handle(ForumThread $forumThread, bool $isHidden, int $hiddenById): ForumThread
    {
        $forumThread->update([
            'is_hidden' => $isHidden,
            'hidden_at' => $isHidden ? now() : null,
            'hidden_by_id' => $isHidden ? $hiddenById : null,
        ]);

        return $forumThread->refresh();
    }
}
