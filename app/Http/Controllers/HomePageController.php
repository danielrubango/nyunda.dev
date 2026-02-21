<?php

namespace App\Http\Controllers;

use App\Actions\Content\ResolveContentTranslation;
use App\Actions\Seo\BuildSeoMeta;
use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomePageController extends Controller
{
    public function __construct(
        private readonly ResolveContentTranslation $resolveContentTranslation,
        private readonly BuildSeoMeta $buildSeoMeta,
    ) {}

    public function __invoke(Request $request): View
    {
        $rows = $this->buildLocalizedRows(
            userLocale: app()->getLocale(),
        );

        $articleRows = $rows
            ->filter(fn (array $row): bool => in_array($row['content_item']->type, [
                ContentType::InternalPost,
                ContentType::ExternalPost,
            ], true))
            ->values();

        $featuredRow = $articleRows
            ->first(fn (array $row): bool => (bool) $row['content_item']->is_featured)
            ?? $articleRows->first();

        $featuredItemId = $featuredRow['content_item']->id ?? null;

        $recentRows = $articleRows
            ->reject(fn (array $row): bool => $featuredItemId !== null && $row['content_item']->id === $featuredItemId)
            ->take(6)
            ->values();

        $popularRows = $articleRows
            ->sortByDesc(function (array $row): int {
                $contentItem = $row['content_item'];

                if ($contentItem->type === ContentType::InternalPost) {
                    return (int) $contentItem->likes_count;
                }

                return 0;
            })
            ->take(4)
            ->values();

        $linkRows = $rows
            ->filter(function (array $row): bool {
                $contentItem = $row['content_item'];
                $externalUrl = $row['translation']->external_url;

                return in_array($contentItem->type, [ContentType::ExternalPost, ContentType::CommunityLink], true)
                    && is_string($externalUrl)
                    && $externalUrl !== '';
            })
            ->take(6)
            ->values();

        return view('home', [
            'featuredRow' => $featuredRow,
            'recentRows' => $recentRows,
            'popularRows' => $popularRows,
            'linkRows' => $linkRows,
            'seo' => $this->buildSeoMeta->handle(
                title: config('app.name'),
                description: __('ui.home.tagline'),
                canonicalUrl: route('home'),
            ),
        ]);
    }

    /**
     * @return Collection<int, array{content_item: ContentItem, translation: ContentTranslation}>
     */
    protected function buildLocalizedRows(?string $userLocale): Collection
    {
        return ContentItem::query()
            ->published()
            ->whereIn('type', [
                ContentType::InternalPost->value,
                ContentType::ExternalPost->value,
                ContentType::CommunityLink->value,
            ])
            ->withCount('likes')
            ->with('author')
            ->with('translations')
            ->latest('published_at')
            ->latest('id')
            ->take(48)
            ->get()
            ->map(function (ContentItem $contentItem) use ($userLocale): ?array {
                $translation = $this->resolveContentTranslation->handle($contentItem, $userLocale);

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
