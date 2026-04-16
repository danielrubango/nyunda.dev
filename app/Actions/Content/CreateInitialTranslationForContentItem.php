<?php

namespace App\Actions\Content;

use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Support\SeoDescription;
use Illuminate\Support\Str;

class CreateInitialTranslationForContentItem
{
    public function __construct(
        private readonly SeoDescription $seoDescription,
    ) {}

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
        $slug = trim((string) ($translationData['initial_slug'] ?? ''));
        $excerpt = trim((string) ($translationData['initial_excerpt'] ?? ''));
        $computedExcerpt = $bodyMarkdown !== ''
            ? $this->seoDescription->generateExcerpt($bodyMarkdown, $title)
            : $this->seoDescription->generatePlainExcerpt($externalDescription, $title);

        return ContentTranslation::query()->create([
            'content_item_id' => $contentItem->id,
            'locale' => (string) ($translationData['initial_locale'] ?? app()->getLocale()),
            'title' => $title,
            'slug' => $slug !== '' ? $slug : Str::slug($title),
            'excerpt' => $excerpt !== '' ? $excerpt : $computedExcerpt,
            'body_markdown' => $bodyMarkdown !== '' ? $bodyMarkdown : null,
            'external_url' => $this->blankToNull($translationData['initial_external_url'] ?? null),
            'external_description' => $this->blankToNull($translationData['initial_external_description'] ?? null),
            'external_site_name' => $this->blankToNull($translationData['initial_external_site_name'] ?? null),
            'external_og_image_url' => $this->blankToNull($translationData['initial_external_og_image_url'] ?? null),
            'featured_image_url' => $this->blankToNull($translationData['initial_featured_image_url'] ?? null),
        ]);
    }

    protected function blankToNull(mixed $value): ?string
    {
        $stringValue = trim((string) $value);

        return $stringValue !== '' ? $stringValue : null;
    }
}
