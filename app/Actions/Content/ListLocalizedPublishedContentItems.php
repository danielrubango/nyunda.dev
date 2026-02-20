<?php

namespace App\Actions\Content;

use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Support\Collection;

class ListLocalizedPublishedContentItems
{
    public function __construct(
        private readonly ResolveContentTranslation $resolveContentTranslation,
    ) {}

    /**
     * @return Collection<int, array{content_item: ContentItem, translation: ContentTranslation}>
     */
    public function handle(
        ?string $filterLocale = null,
        ?string $userLocale = null,
        ?string $contentType = null,
    ): Collection {
        $query = ContentItem::query()
            ->published()
            ->with('author')
            ->withCount('likes')
            ->latest('published_at')
            ->latest('id');

        if ($contentType !== null) {
            $query->where('type', $contentType);
        }

        if ($filterLocale !== null) {
            $items = $query
                ->withWhereHas('translations', function ($translationQuery) use ($filterLocale): void {
                    $translationQuery->where('locale', $filterLocale);
                })
                ->get();

            return $items->map(function (ContentItem $item): array {
                return [
                    'content_item' => $item,
                    'translation' => $item->translations->first(),
                ];
            });
        }

        $items = $query
            ->with('translations')
            ->get();

        return $items
            ->map(function (ContentItem $item) use ($userLocale): ?array {
                $translation = $this->resolveContentTranslation->handle($item, $userLocale);

                if ($translation === null) {
                    return null;
                }

                return [
                    'content_item' => $item,
                    'translation' => $translation,
                ];
            })
            ->filter()
            ->values();
    }
}
