<?php

namespace App\Actions\Content;

use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Pagination\LengthAwarePaginator;
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

    /**
     * @return LengthAwarePaginator<int, array{content_item: ContentItem, translation: ContentTranslation}>
     */
    public function paginate(
        ?string $filterLocale = null,
        ?string $userLocale = null,
        ?string $contentType = null,
        ?array $allowedContentTypes = null,
        ?string $tagSlug = null,
        ?string $search = null,
        int $perPage = 12,
    ): LengthAwarePaginator {
        $query = ContentItem::query()
            ->published()
            ->with(['author', 'tags'])
            ->withCount('likes')
            ->latest('published_at')
            ->latest('id');

        if (is_array($allowedContentTypes) && $allowedContentTypes !== []) {
            $query->whereIn('type', $allowedContentTypes);
        }

        if ($contentType !== null) {
            $query->where('type', $contentType);
        }

        if ($tagSlug !== null) {
            $query->whereHas('tags', function ($tagQuery) use ($tagSlug): void {
                $tagQuery->where('slug', $tagSlug);
            });
        }

        if ($search !== null) {
            $query->whereHas('translations', function ($translationQuery) use ($search): void {
                $translationQuery
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhere('excerpt', 'like', '%'.$search.'%');
            });
        }

        if ($filterLocale !== null) {
            $query->withWhereHas('translations', function ($translationQuery) use ($filterLocale, $search): void {
                $translationQuery->where('locale', $filterLocale);

                if ($search !== null) {
                    $translationQuery->where(function ($nestedTranslationQuery) use ($search): void {
                        $nestedTranslationQuery
                            ->where('title', 'like', '%'.$search.'%')
                            ->orWhere('excerpt', 'like', '%'.$search.'%');
                    });
                }
            });
        } else {
            $query->with('translations');
        }

        /** @var LengthAwarePaginator<int, ContentItem> $paginator */
        $paginator = $query->paginate($perPage)->withQueryString();

        $rows = $paginator->getCollection()
            ->map(function (ContentItem $item) use ($filterLocale, $userLocale): ?array {
                if ($filterLocale !== null) {
                    $translation = $item->translations->first();
                } else {
                    $translation = $this->resolveContentTranslation->handle($item, $userLocale);
                }

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

        $paginator->setCollection($rows);

        /** @var LengthAwarePaginator<int, array{content_item: ContentItem, translation: ContentTranslation}> $paginator */
        return $paginator;
    }
}
