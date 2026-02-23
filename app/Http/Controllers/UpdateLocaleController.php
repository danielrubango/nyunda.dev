<?php

namespace App\Http\Controllers;

use App\Models\ContentTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateLocaleController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $supportedLocales = config('app.supported_locales', ['fr', 'en']);

        $validated = $request->validate([
            'locale' => [
                'required',
                'string',
                Rule::in($supportedLocales),
            ],
            'current_content_locale' => [
                'nullable',
                'string',
                Rule::in($supportedLocales),
            ],
            'current_content_slug' => ['nullable', 'string', 'max:255'],
        ]);

        $locale = (string) $validated['locale'];

        $request->session()->put('preferred_locale', $locale);

        if ($request->user() !== null) {
            $request->user()->forceFill([
                'preferred_locale' => $locale,
            ])->save();
        }

        $redirectToArticle = $this->resolveArticleTranslationRedirect(
            targetLocale: $locale,
            currentContentLocale: $validated['current_content_locale'] ?? null,
            currentContentSlug: $validated['current_content_slug'] ?? null,
        );

        if ($redirectToArticle instanceof RedirectResponse) {
            return $redirectToArticle;
        }

        return redirect()->back();
    }

    private function resolveArticleTranslationRedirect(
        string $targetLocale,
        mixed $currentContentLocale,
        mixed $currentContentSlug,
    ): ?RedirectResponse {
        if (! is_string($currentContentLocale) || $currentContentLocale === '') {
            return null;
        }

        if (! is_string($currentContentSlug) || $currentContentSlug === '') {
            return null;
        }

        if ($currentContentLocale === $targetLocale) {
            return null;
        }

        $currentTranslation = ContentTranslation::query()
            ->where('locale', $currentContentLocale)
            ->where('slug', $currentContentSlug)
            ->whereHas('contentItem', fn ($query) => $query->published())
            ->first();

        if (! $currentTranslation instanceof ContentTranslation) {
            return null;
        }

        $targetTranslation = ContentTranslation::query()
            ->where('content_item_id', $currentTranslation->content_item_id)
            ->where('locale', $targetLocale)
            ->whereHas('contentItem', fn ($query) => $query->published())
            ->first();

        if (! $targetTranslation instanceof ContentTranslation) {
            return null;
        }

        return redirect()->route('blog.show', [
            'locale' => $targetTranslation->locale,
            'slug' => $targetTranslation->slug,
        ]);
    }
}
