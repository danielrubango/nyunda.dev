<?php

namespace App\Actions\Comments;

use App\Models\Comment;

class DeleteComment
{
    public function handle(Comment $comment): void
    {
        $comment->delete();
    }
}
