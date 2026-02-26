<?php

namespace App\Actions\Content;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateDashboardContentSubmission
{
    public function __construct(
        public DeterminePublishingStatus $determinePublishingStatus,
    ) {}

    /**
     * @param  array{
     *     type: ContentType,
     *     locale: string,
     *     title: string,
     *     excerpt: ?string,
     *     body_markdown: ?string,
     *     external_url: ?string,
     *     external_description: ?string,
     *     external_site_name: ?string,
     *     featured_image_url: ?string
     * }  $submission
     */
    public function handle(User $author, array $submission): ContentItem
    {
        return DB::transaction(function () use ($author, $submission): ContentItem {
            $status = $this->determinePublishingStatus->handle($author);

            $contentItem = ContentItem::query()->create([
                'type' => $submission['type']->value,
                'status' => $status->value,
                'author_id' => $author->id,
                'approved_at' => $status === ContentStatus::Published ? now() : null,
                'published_at' => $status === ContentStatus::Published ? now() : null,
                'show_likes' => $submission['type'] === ContentType::InternalPost,
                'show_comments' => $submission['type'] === ContentType::InternalPost,
                'share_on_publish' => false,
                'reads_count' => 0,
                'is_featured' => false,
            ]);

            $excerpt = $submission['excerpt'] !== null
                ? $submission['excerpt']
                : $this->resolveExcerpt($submission['title'], $submission['body_markdown'], $submission['external_description']);

            $contentItem->translations()->create([
                'locale' => $submission['locale'],
                'title' => $submission['title'],
                'slug' => $this->resolveUniqueSlug($submission['locale'], $submission['title']),
                'excerpt' => $excerpt,
                'body_markdown' => $submission['type'] === ContentType::InternalPost ? $submission['body_markdown'] : null,
                'external_url' => $submission['type'] !== ContentType::InternalPost ? $submission['external_url'] : null,
                'external_description' => $submission['type'] !== ContentType::InternalPost ? $submission['external_description'] : null,
                'external_site_name' => $submission['type'] !== ContentType::InternalPost ? $submission['external_site_name'] : null,
                'external_og_image_url' => null,
                'featured_image_url' => $submission['featured_image_url'],
            ]);

            return $contentItem;
        });
    }

    protected function resolveExcerpt(string $title, ?string $bodyMarkdown, ?string $externalDescription): string
    {
        if (is_string($bodyMarkdown) && $bodyMarkdown !== '') {
            return Str::limit(trim((string) Str::of(strip_tags((string) Str::markdown($bodyMarkdown)))->squish()), 200, '');
        }

        if (is_string($externalDescription) && $externalDescription !== '') {
            return Str::limit(trim((string) Str::of($externalDescription)->squish()), 200, '');
        }

        return Str::limit($title, 200, '');
    }

    protected function resolveUniqueSlug(string $locale, string $title): string
    {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'content-item';
        }

        $existingSlugs = ContentTranslation::query()
            ->where('locale', $locale)
            ->where(function ($query) use ($baseSlug): void {
                $query->where('slug', $baseSlug)
                    ->orWhere('slug', 'like', $baseSlug.'-%');
            })
            ->pluck('slug')
            ->all();

        if (! in_array($baseSlug, $existingSlugs, true)) {
            return $baseSlug;
        }

        $maxSuffix = 1;
        $pattern = '/^'.preg_quote($baseSlug, '/').'-(\d+)$/';

        foreach ($existingSlugs as $existingSlug) {
            if (preg_match($pattern, $existingSlug, $matches) !== 1) {
                continue;
            }

            $suffix = (int) $matches[1];

            if ($suffix > $maxSuffix) {
                $maxSuffix = $suffix;
            }
        }

        return $baseSlug.'-'.($maxSuffix + 1);
    }
}
