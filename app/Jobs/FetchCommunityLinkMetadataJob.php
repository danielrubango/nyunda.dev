<?php

namespace App\Jobs;

use App\Actions\Content\FetchOpenGraphData;
use App\Models\ContentTranslation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class FetchCommunityLinkMetadataJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $contentTranslationId,
        public bool $shouldUpdateTitle = false,
        public bool $shouldUpdateExcerpt = false,
    ) {}

    public function handle(FetchOpenGraphData $fetchOpenGraphData): void
    {
        $translation = ContentTranslation::query()->find($this->contentTranslationId);

        if ($translation === null || $translation->external_url === null) {
            return;
        }

        $metadata = $fetchOpenGraphData->handle($translation->external_url);

        $payload = [];

        if ($this->shouldUpdateTitle && $metadata['title'] !== null) {
            $payload['title'] = Str::limit($metadata['title'], 255, '');
        }

        if ($this->shouldUpdateExcerpt && $metadata['description'] !== null) {
            $payload['excerpt'] = Str::limit($metadata['description'], 1000, '');
        }

        if ($translation->external_description === null && $metadata['description'] !== null) {
            $payload['external_description'] = Str::limit($metadata['description'], 2000, '');
        }

        if ($translation->external_site_name === null && $metadata['site_name'] !== null) {
            $payload['external_site_name'] = Str::limit($metadata['site_name'], 255, '');
        }

        if ($metadata['image_url'] !== null) {
            $payload['external_og_image_url'] = Str::limit($metadata['image_url'], 2048, '');
        }

        if ($payload === []) {
            return;
        }

        $translation->forceFill($payload)->save();
    }
}
