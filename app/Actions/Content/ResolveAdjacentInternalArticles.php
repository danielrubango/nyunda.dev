<?php

namespace App\Actions\Content;

use App\Enums\ContentType;
use App\Models\ContentItem;

class ResolveAdjacentInternalArticles
{
    public function __construct(
        private readonly ResolveContentTranslation $resolveContentTranslation,
    ) {}

    /**
     * @return array{
     *     previous: array{title: string, url: string, locale: string}|null,
     *     next: array{title: string, url: string, locale: string}|null
     * }
     */
    public function handle(ContentItem $contentItem, string $preferredLocale): array
    {
        if (! $contentItem->isInternalPost()) {
            return [
                'previous' => null,
                'next' => null,
            ];
        }

        return [
            'previous' => $this->resolveLinkData(
                contentItem: $this->resolvePreviousItem($contentItem),
                preferredLocale: $preferredLocale,
            ),
            'next' => $this->resolveLinkData(
                contentItem: $this->resolveNextItem($contentItem),
                preferredLocale: $preferredLocale,
            ),
        ];
    }

    protected function resolvePreviousItem(ContentItem $contentItem): ?ContentItem
    {
        $manualItem = $this->resolveManualItem(
            linkedArticleId: $contentItem->prev_article_id,
            currentContentItemId: $contentItem->id,
        );

        if ($manualItem !== null) {
            return $manualItem;
        }

        return $this->resolveFallbackItem($contentItem, 'previous');
    }

    protected function resolveNextItem(ContentItem $contentItem): ?ContentItem
    {
        $manualItem = $this->resolveManualItem(
            linkedArticleId: $contentItem->next_article_id,
            currentContentItemId: $contentItem->id,
        );

        if ($manualItem !== null) {
            return $manualItem;
        }

        return $this->resolveFallbackItem($contentItem, 'next');
    }

    protected function resolveManualItem(?int $linkedArticleId, int $currentContentItemId): ?ContentItem
    {
        if ($linkedArticleId === null || $linkedArticleId === $currentContentItemId) {
            return null;
        }

        return ContentItem::query()
            ->whereKey($linkedArticleId)
            ->whereKeyNot($currentContentItemId)
            ->where('type', ContentType::InternalPost->value)
            ->published()
            ->with('translations')
            ->first();
    }

    protected function resolveFallbackItem(ContentItem $contentItem, string $direction): ?ContentItem
    {
        $referenceDate = ($contentItem->published_at ?? $contentItem->created_at ?? now())->toDateTimeString();
        $referenceId = (int) $contentItem->id;

        $query = ContentItem::query()
            ->whereKeyNot($referenceId)
            ->where('type', ContentType::InternalPost->value)
            ->published()
            ->with('translations');

        if ($direction === 'previous') {
            return $query
                ->whereRaw(
                    '(COALESCE(published_at, created_at) < ?) OR (COALESCE(published_at, created_at) = ? AND id < ?)',
                    [$referenceDate, $referenceDate, $referenceId],
                )
                ->orderByRaw('COALESCE(published_at, created_at) desc')
                ->orderByDesc('id')
                ->first();
        }

        return $query
            ->whereRaw(
                '(COALESCE(published_at, created_at) > ?) OR (COALESCE(published_at, created_at) = ? AND id > ?)',
                [$referenceDate, $referenceDate, $referenceId],
            )
            ->orderByRaw('COALESCE(published_at, created_at) asc')
            ->orderBy('id')
            ->first();
    }

    /**
     * @return array{title: string, url: string, locale: string}|null
     */
    protected function resolveLinkData(?ContentItem $contentItem, string $preferredLocale): ?array
    {
        if ($contentItem === null || ! $contentItem->isInternalPost()) {
            return null;
        }

        $translation = $this->resolveContentTranslation->handle($contentItem, $preferredLocale);

        if ($translation === null || $translation->external_url !== null) {
            return null;
        }

        return [
            'title' => $translation->title,
            'url' => route('blog.show', [
                'locale' => $translation->locale,
                'slug' => $translation->slug,
            ]),
            'locale' => $translation->locale,
        ];
    }
}
