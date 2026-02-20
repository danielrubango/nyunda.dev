<?php

namespace App\Actions\Comments;

use App\Models\Comment;

class UpdateCommentVisibility
{
    public function handle(Comment $comment, bool $isHidden, int $hiddenById): Comment
    {
        $comment->forceFill([
            'is_hidden' => $isHidden,
            'hidden_at' => $isHidden ? now() : null,
            'hidden_by_id' => $isHidden ? $hiddenById : null,
        ])->save();

        return $comment;
    }
}
