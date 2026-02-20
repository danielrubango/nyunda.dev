<?php

namespace App\Actions\Content;

use App\Models\ContentItem;
use App\Models\ContentTranslation;

class ResolveContentTranslation
{
    public function handle(ContentItem $contentItem, ?string $preferredLocale = null): ?ContentTranslation
    {
        $translations = $contentItem->relationLoaded('translations')
            ? $contentItem->translations
            : $contentItem->translations()->get();

        if ($translations->isEmpty()) {
            return null;
        }

        $locale = $preferredLocale ?? app()->getLocale();

        return $translations->firstWhere('locale', $locale)
            ?? $translations->firstWhere('locale', 'fr')
            ?? $translations->sortBy('id')->first();
    }
}
