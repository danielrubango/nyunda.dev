<?php

namespace App\Actions\Content;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Jobs\FetchCommunityLinkMetadataJob;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubmitCommunityLink
{
    /**
     * @param array{
     *     locale: string,
     *     title: ?string,
     *     excerpt: ?string,
     *     external_url: string,
     *     external_description: ?string,
     *     external_site_name: ?string
     * } $submission
     */
    public function handle(User $author, array $submission): ContentItem
    {
        return DB::transaction(function () use ($author, $submission): ContentItem {
            $contentItem = ContentItem::query()->create([
                'type' => ContentType::ExternalPost->value,
                'status' => ContentStatus::Pending->value,
                'author_id' => $author->id,
                'show_likes' => false,
                'show_comments' => false,
                'share_on_publish' => false,
            ]);

            $title = $this->resolveTitle(
                title: $submission['title'],
                externalUrl: $submission['external_url'],
            );

            $excerpt = $this->resolveExcerpt(
                excerpt: $submission['excerpt'],
                externalUrl: $submission['external_url'],
            );

            $translation = $contentItem->translations()->create([
                'locale' => $submission['locale'],
                'title' => $title,
                'slug' => $this->resolveUniqueSlug(
                    locale: $submission['locale'],
                    title: $title,
                ),
                'excerpt' => $excerpt,
                'body_markdown' => null,
                'external_url' => $submission['external_url'],
                'external_description' => $submission['external_description'],
                'external_site_name' => $submission['external_site_name'],
                'external_og_image_url' => null,
            ]);

            FetchCommunityLinkMetadataJob::dispatch(
                contentTranslationId: $translation->id,
                shouldUpdateTitle: $submission['title'] === null,
                shouldUpdateExcerpt: $submission['excerpt'] === null,
            )->afterCommit();

            return $contentItem;
        });
    }

    protected function resolveUniqueSlug(string $locale, string $title): string
    {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'external-link';
        }

        $existingSlugs = ContentTranslation::query()
            ->where('locale', $locale)
            ->where(function ($query) use ($baseSlug): void {
                $query
                    ->where('slug', $baseSlug)
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

    protected function resolveTitle(?string $title, string $externalUrl): string
    {
        if (is_string($title) && trim($title) !== '') {
            return trim($title);
        }

        return 'Ressource partagee '.$this->resolveHostLabel($externalUrl);
    }

    protected function resolveExcerpt(?string $excerpt, string $externalUrl): string
    {
        if (is_string($excerpt) && trim($excerpt) !== '') {
            return trim($excerpt);
        }

        return 'Ressource externe en attente de metadonnees pour '.$this->resolveHostLabel($externalUrl).'.';
    }

    protected function resolveHostLabel(string $externalUrl): string
    {
        $host = parse_url($externalUrl, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return 'source externe';
        }

        return Str::of($host)
            ->replaceStart('www.', '')
            ->value();
    }
}
