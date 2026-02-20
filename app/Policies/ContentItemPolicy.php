<?php

namespace App\Policies;

use App\Models\ContentItem;
use App\Models\User;

class ContentItemPolicy
{
    public function comment(User $user, ContentItem $contentItem): bool
    {
        return $contentItem->isPublished()
            && $contentItem->isInternalPost()
            && $contentItem->show_comments;
    }

    public function like(User $user, ContentItem $contentItem): bool
    {
        return $contentItem->isPublished() && $contentItem->isInternalPost();
    }
}
