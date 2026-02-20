<?php

namespace App\Actions\Comments;

use App\Models\ContentItem;
use App\Models\ContentLike;
use App\Models\User;

class ToggleContentLike
{
    public function handle(User $user, ContentItem $contentItem): bool
    {
        $existingLike = ContentLike::query()
            ->where('content_item_id', $contentItem->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike !== null) {
            $existingLike->delete();

            return false;
        }

        ContentLike::query()->create([
            'content_item_id' => $contentItem->id,
            'user_id' => $user->id,
        ]);

        return true;
    }
}
