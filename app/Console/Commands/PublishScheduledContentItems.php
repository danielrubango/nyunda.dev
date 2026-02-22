<?php

namespace App\Console\Commands;

use App\Actions\Content\TransitionContentItemStatus;
use App\Enums\ContentStatus;
use App\Models\ContentItem;
use Illuminate\Console\Command;

class PublishScheduledContentItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content-items:publish-scheduled {--chunk=100 : Number of scheduled items to process per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled content items that reached their publication datetime.';

    /**
     * Execute the console command.
     */
    public function handle(TransitionContentItemStatus $transitionContentItemStatus): int
    {
        $chunkSize = max((int) $this->option('chunk'), 1);
        $processedCount = 0;

        ContentItem::query()
            ->where('status', ContentStatus::Pending->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('id')
            ->chunkById($chunkSize, function ($contentItems) use (
                &$processedCount,
                $transitionContentItemStatus,
            ): void {
                foreach ($contentItems as $contentItem) {
                    $transitionContentItemStatus->handle(
                        $contentItem,
                        ContentStatus::Published,
                        publishedAt: $contentItem->published_at,
                    );

                    $processedCount++;
                }
            });

        $this->info(sprintf('Published %d scheduled content item(s).', $processedCount));

        return self::SUCCESS;
    }
}
