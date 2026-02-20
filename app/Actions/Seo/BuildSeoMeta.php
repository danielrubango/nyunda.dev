<?php

namespace App\Actions\Seo;

use Illuminate\Support\Str;

class BuildSeoMeta
{
    /**
     * @return array{
     *     title: string,
     *     description: string,
     *     canonical_url: string|null,
     *     og_type: string,
     *     image_url: string|null,
     *     site_name: string,
     *     twitter_card: string
     * }
     */
    public function handle(
        string $title,
        ?string $description = null,
        ?string $canonicalUrl = null,
        ?string $imageUrl = null,
        string $ogType = 'website',
    ): array {
        $resolvedTitle = $this->resolveTitle($title);
        $resolvedDescription = $this->resolveDescription($description);
        $resolvedCanonicalUrl = $this->normalizeUrl($canonicalUrl);
        $resolvedImageUrl = $this->normalizeUrl($imageUrl);

        return [
            'title' => $resolvedTitle,
            'description' => $resolvedDescription,
            'canonical_url' => $resolvedCanonicalUrl,
            'og_type' => $ogType,
            'image_url' => $resolvedImageUrl,
            'site_name' => (string) config('app.name'),
            'twitter_card' => $resolvedImageUrl === null ? 'summary' : 'summary_large_image',
        ];
    }

    protected function resolveTitle(string $title): string
    {
        $applicationName = (string) config('app.name');
        $cleanTitle = trim($title);

        if ($cleanTitle === '') {
            return $applicationName;
        }

        if ($cleanTitle === $applicationName || Str::contains($cleanTitle, ' | '.$applicationName)) {
            return $cleanTitle;
        }

        return $cleanTitle.' | '.$applicationName;
    }

    protected function resolveDescription(?string $description): string
    {
        $cleanDescription = Str::of((string) $description)
            ->stripTags()
            ->squish()
            ->value();

        if ($cleanDescription === '') {
            $cleanDescription = 'Blog technique sur PHP, Laravel et IA.';
        }

        return Str::limit($cleanDescription, 160, '');
    }

    protected function normalizeUrl(?string $url): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $trimmedUrl = trim($url);

        return $trimmedUrl === '' ? null : $trimmedUrl;
    }
}
