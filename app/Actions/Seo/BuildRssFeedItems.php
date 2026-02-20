<?php

namespace App\Actions\Seo;

use App\Actions\Content\ResolveContentTranslation;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class BuildRssFeedItems
{
    public function __construct(
        private readonly ResolveContentTranslation $resolveContentTranslation,
    ) {}

    /**
     * @return Collection<int, array{
     *     title: string,
     *     link: string,
     *     guid: string,
     *     description: string,
     *     published_at: CarbonInterface,
     *     locale: string
     * }>
     */
    public function handle(?string $preferredLocale = null): Collection
    {
        $items = ContentItem::query()
            ->published()
            ->with('translations')
            ->latest('published_at')
            ->latest('id')
            ->limit(50)
            ->get();

        return $items
            ->map(function (ContentItem $contentItem) use ($preferredLocale): ?array {
                $translation = $this->resolveContentTranslation->handle($contentItem, $preferredLocale);

                if ($translation === null) {
                    return null;
                }

                $link = $this->resolvePublicLink($contentItem, $translation);

                if ($link === null) {
                    return null;
                }

                $canonicalLink = route('blog.show', [
                    'locale' => $translation->locale,
                    'slug' => $translation->slug,
                ]);

                $description = $translation->excerpt
                    ?: ($translation->external_description ?: $translation->title);

                return [
                    'title' => $translation->title,
                    'link' => $link,
                    'guid' => $canonicalLink,
                    'description' => $description,
                    'published_at' => $contentItem->published_at ?? $contentItem->created_at ?? now(),
                    'locale' => $translation->locale,
                ];
            })
            ->filter()
            ->values();
    }

    protected function resolvePublicLink(ContentItem $contentItem, ContentTranslation $translation): ?string
    {
        if ($contentItem->isInternalPost()) {
            return route('blog.show', [
                'locale' => $translation->locale,
                'slug' => $translation->slug,
            ]);
        }

        return $translation->external_url;
    }
}
