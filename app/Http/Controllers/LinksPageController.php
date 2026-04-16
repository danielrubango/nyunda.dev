<?php

namespace App\Http\Controllers;

use App\Actions\Content\ResolveContentTranslation;
use App\Actions\Seo\BuildSeoMeta;
use App\Enums\ContentType;
use App\Http\Requests\Blog\ListLinksRequest;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LinksPageController extends Controller
{
    public function __construct(
        private readonly ResolveContentTranslation $resolveContentTranslation,
        private readonly BuildSeoMeta $buildSeoMeta,
    ) {}

    public function __invoke(ListLinksRequest $request): View
    {
        $query = ContentItem::query()
            ->published()
            ->where('type', ContentType::ExternalPost->value)
            ->with('author')
            ->latest('published_at')
            ->latest('id');

        if ($request->searchTerm() !== null) {
            $searchTerm = $request->searchTerm();

            $query->whereHas('translations', function ($translationQuery) use ($searchTerm): void {
                $translationQuery->where(function ($nestedTranslationQuery) use ($searchTerm): void {
                    $nestedTranslationQuery
                        ->where('title', 'like', '%'.$searchTerm.'%')
                        ->orWhere('excerpt', 'like', '%'.$searchTerm.'%')
                        ->orWhere('external_description', 'like', '%'.$searchTerm.'%')
                        ->orWhere('external_site_name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('external_url', 'like', '%'.$searchTerm.'%');
                });
            });
        }

        if ($request->localeFilter() !== null) {
            $query->withWhereHas('translations', function ($translationQuery) use ($request): void {
                $translationQuery
                    ->where('locale', $request->localeFilter())
                    ->whereNotNull('external_url');
            });
        } else {
            $query->with('translations');
        }

        /** @var LengthAwarePaginator<int, ContentItem> $links */
        $links = $query->paginate(12)->withQueryString();

        $rows = $links->getCollection()
            ->map(function (ContentItem $contentItem) use ($request): ?array {
                if ($request->localeFilter() !== null) {
                    $translation = $contentItem->translations->first();
                } else {
                    $translation = $this->resolveContentTranslation->handle(
                        $contentItem,
                        $request->resolvedUserLocale(),
                    );
                }

                if ($translation === null || ! is_string($translation->external_url) || $translation->external_url === '') {
                    return null;
                }

                return [
                    'content_item' => $contentItem,
                    'translation' => $translation,
                ];
            })
            ->filter(function (?array $row) use ($request): bool {
                if (! is_array($row)) {
                    return false;
                }

                $searchTerm = $request->searchTerm();

                if ($searchTerm === null) {
                    return true;
                }

                /** @var ContentTranslation $translation */
                $translation = $row['translation'];

                return $this->translationMatchesSearch($translation, $searchTerm);
            })
            ->filter()
            ->values();

        $links->setCollection($rows);

        return view('links.index', [
            'rows' => $links,
            'selectedLocale' => $request->localeSelection(),
            'searchTerm' => $request->searchTerm(),
            'supportedLocales' => config('app.supported_locales', ['fr', 'en']),
            'seo' => $this->buildSeoMeta->handle(
                title: __('ui.links.title'),
                description: __('ui.seo.meta.links'),
                canonicalUrl: $request->fullUrl(),
            ),
        ]);
    }

    protected function translationMatchesSearch(ContentTranslation $translation, string $searchTerm): bool
    {
        $normalizedSearch = Str::lower($searchTerm);
        $haystack = Str::lower(implode(' ', array_filter([
            $translation->title,
            $translation->excerpt,
            $translation->external_description,
            $translation->external_site_name,
            $translation->external_url,
        ])));

        return Str::contains($haystack, $normalizedSearch);
    }
}
