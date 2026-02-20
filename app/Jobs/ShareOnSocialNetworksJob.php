<?php

namespace App\Jobs;

use App\Actions\Content\ShareOnSocialNetworks;
use App\Models\ContentItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ShareOnSocialNetworksJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @var list<int>
     */
    public array $backoff = [60, 300, 900];

    public function __construct(
        public int $contentItemId,
    ) {}

    public function handle(ShareOnSocialNetworks $shareOnSocialNetworks): void
    {
        $contentItem = ContentItem::query()
            ->with('translations')
            ->find($this->contentItemId);

        if ($contentItem === null) {
            return;
        }

        $shareOnSocialNetworks->handle($contentItem);
    }
}
