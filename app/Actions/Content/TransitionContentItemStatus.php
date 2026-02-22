<?php

namespace App\Actions\Content;

use App\Enums\ContentStatus;
use App\Models\ContentItem;
use Carbon\CarbonInterface;

class TransitionContentItemStatus
{
    public function handle(
        ContentItem $contentItem,
        ContentStatus $targetStatus,
        ?CarbonInterface $publishedAt = null,
    ): ContentItem {
        $payload = [
            'status' => $targetStatus->value,
        ];

        if ($targetStatus === ContentStatus::Published) {
            $payload['approved_at'] = $contentItem->approved_at ?? now();
            $payload['published_at'] = $publishedAt ?? now();
        } elseif ($targetStatus === ContentStatus::Pending && $publishedAt !== null) {
            $payload['approved_at'] = null;
            $payload['published_at'] = $publishedAt;
        } else {
            $payload['approved_at'] = null;
            $payload['published_at'] = null;
        }

        $payloadIsUnchanged = $contentItem->status === $targetStatus
            && $this->sameDateTime($contentItem->approved_at, $payload['approved_at'])
            && $this->sameDateTime($contentItem->published_at, $payload['published_at']);

        if ($payloadIsUnchanged) {
            return $contentItem;
        }

        $contentItem->update($payload);

        return $contentItem->refresh();
    }

    protected function sameDateTime(?CarbonInterface $current, mixed $next): bool
    {
        if ($current === null && $next === null) {
            return true;
        }

        if ($current === null || ! $next instanceof CarbonInterface) {
            return false;
        }

        return $current->equalTo($next);
    }
}
