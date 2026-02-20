<?php

namespace App\Http\Controllers\Blog;

use App\Actions\Content\GetPublishedContentTranslationBySlug;
use App\Actions\Content\ListLocalizedPublishedContentItems;
use App\Actions\Content\RenderSafeMarkdown;
use App\Actions\Seo\BuildSeoMeta;
use App\Enums\ContentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\ListBlogContentRequest;
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
        $rows = $this->listLocalizedPublishedContentItems->handle(
            filterLocale: $request->localeFilter(),
            userLocale: $request->resolvedUserLocale(),
            contentType: $request->typeFilter(),
        );

        return view('blog.index', [
            'rows' => $rows,
            'selectedLocale' => $request->localeSelection(),
            'selectedType' => $request->typeFilter(),
            'supportedLocales' => config('app.supported_locales', ['fr', 'en']),
            'seo' => $this->buildSeoMeta->handle(
                title: 'Blog',
                description: 'Articles techniques, veille Laravel et contenu communautaire.',
                canonicalUrl: $request->fullUrl(),
            ),
        ]);
    }

    public function show(string $locale, string $slug): View|RedirectResponse
    {
        abort_unless(in_array($locale, config('app.supported_locales', ['fr', 'en']), true), 404);

        $translation = $this->getPublishedContentTranslationBySlug->handle($locale, $slug);

        abort_if($translation === null, 404);

        $contentItem = $translation->contentItem;

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
                imageUrl: $translation->external_og_image_url,
                ogType: 'article',
            ),
            'renderedComments' => $comments->mapWithKeys(
                fn ($comment): array => [
                    $comment->id => $this->renderSafeMarkdown->handle($comment->body_markdown),
                ],
            ),
        ]);
    }
}
