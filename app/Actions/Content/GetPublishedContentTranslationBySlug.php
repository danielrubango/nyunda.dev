<?php

namespace App\Actions\Content;

use App\Models\ContentTranslation;

class GetPublishedContentTranslationBySlug
{
    public function handle(string $locale, string $slug): ?ContentTranslation
    {
        return ContentTranslation::query()
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->whereHas('contentItem', fn ($query) => $query->published())
            ->with('contentItem.author')
            ->first();
    }
}
