<?php

namespace App\Actions\Seo;

use App\Support\SeoDescription;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;

class BuildSeoMeta
{
    public function __construct(
        private readonly SeoDescription $seoDescription,
    ) {}

    /**
     * @return array{
     *     title: string,
     *     description: string,
     *     canonical_url: string|null,
     *     og_type: string,
     *     image_url: string|null,
     *     site_name: string,
     *     twitter_card: string,
     *     robots: string,
     *     alternates: array<string, string>,
     *     schema: array<int, array<string, mixed>>,
     *     article: array{published_time: string|null, modified_time: string|null, author: string|null}
     * }
     */
    public function handle(
        string $title,
        ?string $description = null,
        ?string $canonicalUrl = null,
        ?string $imageUrl = null,
        string $ogType = 'website',
        ?string $robots = null,
        array $alternates = [],
        array $schema = [],
        ?CarbonInterface $publishedAt = null,
        ?CarbonInterface $modifiedAt = null,
        ?string $author = null,
        ?string $fallbackDescription = null,
    ): array {
        $resolvedTitle = $this->resolveTitle($title);
        $resolvedDescription = $this->resolveDescription($description, $fallbackDescription, $title);
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
            'robots' => $this->resolveRobots($robots),
            'alternates' => $this->resolveAlternates($alternates),
            'schema' => $this->resolveSchema($schema),
            'article' => [
                'published_time' => $publishedAt?->toIso8601String(),
                'modified_time' => $modifiedAt?->toIso8601String(),
                'author' => $author !== null && trim($author) !== '' ? trim($author) : null,
            ],
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

    protected function resolveDescription(?string $description, ?string $fallbackDescription, ?string $title = null): string
    {
        return $this->seoDescription->forMeta(
            description: $description,
            fallback: $fallbackDescription ?? __('ui.seo.default_description'),
            title: $title,
        );
    }

    protected function normalizeUrl(?string $url): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $trimmedUrl = trim($url);

        return $trimmedUrl === '' ? null : $trimmedUrl;
    }

    protected function resolveRobots(?string $robots): string
    {
        $normalizedRobots = trim((string) $robots);

        return $normalizedRobots === '' ? 'index,follow' : $normalizedRobots;
    }

    /**
     * @param  array<string, string|null>  $alternates
     * @return array<string, string>
     */
    protected function resolveAlternates(array $alternates): array
    {
        return collect($alternates)
            ->mapWithKeys(function (mixed $url, mixed $locale): array {
                $normalizedLocale = is_string($locale) ? trim($locale) : '';
                $normalizedUrl = $this->normalizeUrl(is_string($url) ? $url : null);

                if ($normalizedLocale === '' || $normalizedUrl === null) {
                    return [];
                }

                return [$normalizedLocale => $normalizedUrl];
            })
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $schema
     * @return array<int, array<string, mixed>>
     */
    protected function resolveSchema(array $schema): array
    {
        return array_values(array_filter(
            $schema,
            fn (mixed $item): bool => is_array($item) && $item !== [],
        ));
    }
}
