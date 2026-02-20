<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use App\Models\ContentItem;
use App\Models\User;

class AddCommentToContentItem
{
    public function handle(User $user, ContentItem $contentItem, string $bodyMarkdown): Comment
    {
        return Comment::query()->create([
            'content_item_id' => $contentItem->id,
            'user_id' => $user->id,
            'body_markdown' => $bodyMarkdown,
        ]);
    }
}
