<?php

namespace App\Observers;

use App\Enums\ContentStatus;
use App\Jobs\ShareOnSocialNetworksJob;
use App\Models\ContentItem;

class ContentItemObserver
{
    public function updated(ContentItem $contentItem): void
    {
        if (! $contentItem->wasChanged('status')) {
            return;
        }

        $previousStatus = (string) $contentItem->getRawOriginal('status');

        if (! in_array($previousStatus, [
            ContentStatus::Draft->value,
            ContentStatus::Pending->value,
        ], true)) {
            return;
        }

        if ($contentItem->status !== ContentStatus::Published) {
            return;
        }

        if (! $contentItem->share_on_publish) {
            return;
        }

        ShareOnSocialNetworksJob::dispatch($contentItem->id)->afterCommit();
    }
}
