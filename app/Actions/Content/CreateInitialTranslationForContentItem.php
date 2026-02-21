<?php

namespace App\Actions\Content;

use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Support\Str;

class CreateInitialTranslationForContentItem
{
    /**
     * @param  array<string, mixed>  $translationData
     */
    public function handle(ContentItem $contentItem, array $translationData): ?ContentTranslation
    {
        $title = trim((string) ($translationData['initial_title'] ?? ''));

        if ($title === '') {
            return null;
        }

        $bodyMarkdown = (string) ($translationData['initial_body_markdown'] ?? '');
        $externalDescription = (string) ($translationData['initial_external_description'] ?? '');
        $computedExcerptSource = $bodyMarkdown !== '' ? $bodyMarkdown : $externalDescription;
        $computedExcerpt = trim((string) Str::of(strip_tags((string) Str::markdown($computedExcerptSource)))->squish());
        $slug = trim((string) ($translationData['initial_slug'] ?? ''));
        $excerpt = trim((string) ($translationData['initial_excerpt'] ?? ''));

        return ContentTranslation::query()->create([
            'content_item_id' => $contentItem->id,
            'locale' => (string) ($translationData['initial_locale'] ?? app()->getLocale()),
            'title' => $title,
            'slug' => $slug !== '' ? $slug : Str::slug($title),
            'excerpt' => $excerpt !== '' ? $excerpt : Str::limit($computedExcerpt, 200),
            'body_markdown' => $bodyMarkdown !== '' ? $bodyMarkdown : null,
            'external_url' => $this->blankToNull($translationData['initial_external_url'] ?? null),
            'external_description' => $this->blankToNull($translationData['initial_external_description'] ?? null),
            'external_site_name' => $this->blankToNull($translationData['initial_external_site_name'] ?? null),
            'external_og_image_url' => $this->blankToNull($translationData['initial_external_og_image_url'] ?? null),
        ]);
    }

    protected function blankToNull(mixed $value): ?string
    {
        $stringValue = trim((string) $value);

        return $stringValue !== '' ? $stringValue : null;
    }
}
