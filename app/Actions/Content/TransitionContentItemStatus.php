<?php

namespace App\Actions\Content;

use App\Enums\ContentStatus;
use App\Models\ContentItem;

class TransitionContentItemStatus
{
    public function handle(ContentItem $contentItem, ContentStatus $targetStatus): ContentItem
    {
        if ($contentItem->status === $targetStatus) {
            return $contentItem;
        }

        $payload = [
            'status' => $targetStatus->value,
        ];

        if ($targetStatus === ContentStatus::Published) {
            $payload['approved_at'] = $contentItem->approved_at ?? now();
            $payload['published_at'] = now();
        } else {
            $payload['approved_at'] = null;
            $payload['published_at'] = null;
        }

        $contentItem->update($payload);

        return $contentItem->refresh();
    }
}
