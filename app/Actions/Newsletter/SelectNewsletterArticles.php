<?php

namespace App\Actions\Newsletter;

use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Support\Collection;

class SelectNewsletterArticles
{
    /**
     * Retourne les articles publiés les plus récents éligibles à la newsletter.
     * Priorité : articles internes en premier, puis articles externes.
     * Limite : 6 articles maximum par défaut.
     *
     * @param  int  $dayRange  Nombre de jours en arrière à considérer (0 = tous)
     * @return Collection<int, array{content_item: ContentItem, translation: ContentTranslation}>
     */
    public function handle(int $limit = 6, int $dayRange = 30): Collection
    {
        $query = ContentItem::query()
            ->published()
            ->whereIn('type', [
                ContentType::InternalPost->value,
                ContentType::ExternalPost->value,
            ])
            ->with('translations')
            ->withCount('likes')
            ->latest('published_at')
            ->latest('id');

        if ($dayRange > 0) {
            $query->where('published_at', '>=', now()->subDays($dayRange));
        }

        return $query
            ->take($limit)
            ->get()
            ->map(function (ContentItem $contentItem): ?array {
                $translation = $contentItem->translations
                    ->firstWhere('locale', config('app.locale', 'fr'))
                    ?? $contentItem->translations->firstWhere('locale', 'fr')
                    ?? $contentItem->translations->first();

                if ($translation === null) {
                    return null;
                }

                return [
                    'content_item' => $contentItem,
                    'translation' => $translation,
                ];
            })
            ->filter()
            ->values();
    }
}
