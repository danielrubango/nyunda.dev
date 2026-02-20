<?php

namespace App\Actions\Content;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class FetchOpenGraphData
{
    /**
     * @return array{
     *     title: ?string,
     *     description: ?string,
     *     site_name: ?string,
     *     image_url: ?string
     * }
     */
    public function handle(string $url): array
    {
        try {
            $response = Http::timeout(8)
                ->connectTimeout(4)
                ->withHeaders([
                    'User-Agent' => 'NYUNDA.DEV Metadata Bot/1.0',
                ])
                ->get($url);
        } catch (Throwable) {
            return $this->emptyMetadata();
        }

        if (! $response->successful()) {
            return $this->emptyMetadata();
        }

        $html = Str::of($response->body())
            ->limit(500000, '')
            ->value();

        $title = $this->extractMetaTag($html, 'og:title')
            ?? $this->extractMetaTag($html, 'twitter:title')
            ?? $this->extractTitleTag($html);

        $description = $this->extractMetaTag($html, 'og:description')
            ?? $this->extractMetaTag($html, 'description');

        $siteName = $this->extractMetaTag($html, 'og:site_name');
        $imageUrl = $this->extractMetaTag($html, 'og:image');

        return [
            'title' => $this->normalizeText($title, 255),
            'description' => $this->normalizeText($description, 2000),
            'site_name' => $this->normalizeText($siteName, 255),
            'image_url' => $this->normalizeText($imageUrl, 2048),
        ];
    }

    /**
     * @return array{title: null, description: null, site_name: null, image_url: null}
     */
    protected function emptyMetadata(): array
    {
        return [
            'title' => null,
            'description' => null,
            'site_name' => null,
            'image_url' => null,
        ];
    }

    protected function extractMetaTag(string $html, string $key): ?string
    {
        $quotedKey = preg_quote($key, '/');

        $patterns = [
            '/<meta[^>]*property=["\']'.$quotedKey.'["\'][^>]*content=["\']([^"\']+)["\'][^>]*>/i',
            '/<meta[^>]*name=["\']'.$quotedKey.'["\'][^>]*content=["\']([^"\']+)["\'][^>]*>/i',
            '/<meta[^>]*content=["\']([^"\']+)["\'][^>]*(property|name)=["\']'.$quotedKey.'["\'][^>]*>/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches) !== 1) {
                continue;
            }

            return $matches[1];
        }

        return null;
    }

    protected function extractTitleTag(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    protected function normalizeText(?string $value, int $maxLength): ?string
    {
        if ($value === null) {
            return null;
        }

        $decoded = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decoded = trim(preg_replace('/\s+/', ' ', $decoded) ?? '');

        if ($decoded === '') {
            return null;
        }

        return Str::limit($decoded, $maxLength, '');
    }
}
