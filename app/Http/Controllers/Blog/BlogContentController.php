<?php

namespace App\Http\Controllers\Blog;

use App\Actions\Content\GetPublishedContentTranslationBySlug;
use App\Actions\Content\ListLocalizedPublishedContentItems;
use App\Actions\Content\RenderSafeMarkdown;
use App\Actions\Seo\BuildSeoMeta;
use App\Enums\ContentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\ListBlogContentRequest;
use App\Models\ContentTranslation;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BlogContentController extends Controller
{
    public function __construct(
        private readonly ListLocalizedPublishedContentItems $listLocalizedPublishedContentItems,
        private readonly GetPublishedContentTranslationBySlug $getPublishedContentTranslationBySlug,
        private readonly RenderSafeMarkdown $renderSafeMarkdown,
        private readonly BuildSeoMeta $buildSeoMeta,
    ) {}

    public function index(ListBlogContentRequest $request): View
    {
        $rows = $this->listLocalizedPublishedContentItems->paginate(
            filterLocale: $request->localeFilter(),
            userLocale: $request->resolvedUserLocale(),
            contentType: $request->typeFilter(),
            allowedContentTypes: [
                ContentType::InternalPost->value,
                ContentType::ExternalPost->value,
            ],
            tagSlug: $request->tagFilter(),
            search: $request->searchTerm(),
        );

        return view('blog.index', [
            'rows' => $rows,
            'selectedLocale' => $request->localeSelection(),
            'selectedType' => $request->typeFilter(),
            'selectedTag' => $request->tagFilter(),
            'searchTerm' => $request->searchTerm(),
            'supportedLocales' => config('app.supported_locales', ['fr', 'en']),
            'tags' => Tag::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']),
            'seo' => $this->buildSeoMeta->handle(
                title: __('ui.blog.title'),
                description: __('ui.blog.subtitle'),
                canonicalUrl: $request->fullUrl(),
            ),
        ]);
    }

    public function show(string $locale, string $slug): View|RedirectResponse
    {
        abort_unless(in_array($locale, config('app.supported_locales', ['fr', 'en']), true), 404);
        app()->setLocale($locale);

        $translation = $this->getPublishedContentTranslationBySlug->handle($locale, $slug);

        abort_if($translation === null, 404);

        $contentItem = $translation->contentItem;
        $contentItem->loadMissing('tags');

        if ($contentItem->type !== ContentType::InternalPost) {
            abort_if($translation->external_url === null, 404);

            return redirect()->away($translation->external_url);
        }

        $contentItem->loadCount('likes');

        /** @var Collection<int, \App\Models\Comment> $comments */
        $comments = collect();

        if ($contentItem->show_comments) {
            $contentItem->load([
                'comments' => fn ($query) => $query
                    ->where('is_hidden', false)
                    ->with('user')
                    ->latest('created_at'),
            ]);

            $comments = $contentItem->comments;
        }

        return view('blog.show', [
            'contentItem' => $contentItem,
            'translation' => $translation,
            'renderedBody' => $this->renderSafeMarkdown->handle($translation->body_markdown),
            'comments' => $comments,
            'seo' => $this->buildSeoMeta->handle(
                title: $translation->title,
                description: $translation->excerpt,
                canonicalUrl: route('blog.show', [
                    'locale' => $translation->locale,
                    'slug' => $translation->slug,
                ]),
                imageUrl: $translation->featured_image_url ?: $translation->external_og_image_url,
                ogType: 'article',
            ),
            'renderedComments' => $comments->mapWithKeys(
                fn ($comment): array => [
                    $comment->id => $this->renderSafeMarkdown->handle($comment->body_markdown),
                ],
            ),
        ]);
    }

    public function showBySlug(string $slug): RedirectResponse
    {
        $translation = ContentTranslation::query()
            ->where('slug', $slug)
            ->whereHas('contentItem', fn ($query) => $query->published())
            ->first();

        abort_if($translation === null, 404);

        return redirect()->route('blog.show', [
            'locale' => $translation->locale,
            'slug' => $translation->slug,
        ], 301);
    }
}
