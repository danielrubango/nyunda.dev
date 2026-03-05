<?php

namespace App\Actions\Content;

use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateDashboardContentSubmission
{
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
    public function handle(ContentItem $contentItem, array $submission): ContentItem
    {
        return DB::transaction(function () use ($contentItem, $submission): ContentItem {
            $contentItem->forceFill([
                'type' => $submission['type']->value,
                'show_likes' => $submission['type'] === ContentType::InternalPost,
                'show_comments' => $submission['type'] === ContentType::InternalPost,
            ])->save();

            $translation = $contentItem->translations()
                ->where('locale', $submission['locale'])
                ->first()
                ?? $contentItem->translations()->first();

            $excerpt = $submission['excerpt'] !== null
                ? $submission['excerpt']
                : $this->resolveExcerpt($submission['title'], $submission['body_markdown'], $submission['external_description']);

            $slug = $this->resolveUniqueSlug(
                locale: $submission['locale'],
                title: $submission['title'],
                ignoredTranslationId: $translation?->id,
            );

            $payload = [
                'locale' => $submission['locale'],
                'title' => $submission['title'],
                'slug' => $slug,
                'excerpt' => $excerpt,
                'body_markdown' => $submission['type'] === ContentType::InternalPost ? $submission['body_markdown'] : null,
                'external_url' => $submission['type'] !== ContentType::InternalPost ? $submission['external_url'] : null,
                'external_description' => $submission['type'] !== ContentType::InternalPost ? $submission['external_description'] : null,
                'external_site_name' => $submission['type'] !== ContentType::InternalPost ? $submission['external_site_name'] : null,
                'featured_image_url' => $submission['featured_image_url'],
            ];

            if ($translation === null) {
                $contentItem->translations()->create($payload);
            } else {
                $translation->forceFill($payload)->save();
            }

            return $contentItem->fresh(['translations']);
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

    protected function resolveUniqueSlug(string $locale, string $title, ?int $ignoredTranslationId = null): string
    {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'content-item';
        }

        $query = ContentTranslation::query()
            ->where('locale', $locale)
            ->where(function ($builder) use ($baseSlug): void {
                $builder->where('slug', $baseSlug)
                    ->orWhere('slug', 'like', $baseSlug.'-%');
            });

        if ($ignoredTranslationId !== null) {
            $query->where('id', '!=', $ignoredTranslationId);
        }

        $existingSlugs = $query->pluck('slug')->all();

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
