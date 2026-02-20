<?php

namespace App\Actions\Content;

use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class ShareOnSocialNetworks
{
    public function handle(ContentItem $contentItem): void
    {
        $translation = $this->resolveTranslation($contentItem);

        if ($translation === null) {
            return;
        }

        $sharedUrl = $this->resolveSharedUrl($contentItem, $translation);

        if ($sharedUrl === null) {
            return;
        }

        $message = $this->formatMessage(
            title: $translation->title,
            url: $sharedUrl,
        );

        $failedPlatforms = [];

        if (! $this->shareOnX($contentItem, $message, $sharedUrl)) {
            $failedPlatforms[] = 'x';
        }

        if (! $this->shareOnLinkedIn($contentItem, $message, $sharedUrl)) {
            $failedPlatforms[] = 'linkedin';
        }

        if ($failedPlatforms !== []) {
            throw new RuntimeException(
                'Social sharing failed for platforms: '.implode(', ', $failedPlatforms),
            );
        }
    }

    protected function resolveTranslation(ContentItem $contentItem): ?ContentTranslation
    {
        $contentItem->loadMissing('translations');

        $appLocale = (string) config('app.locale', 'fr');

        return $contentItem->translations
            ->firstWhere('locale', $appLocale)
            ?? $contentItem->translations->firstWhere('locale', 'fr')
            ?? $contentItem->translations->first();
    }

    protected function resolveSharedUrl(ContentItem $contentItem, ContentTranslation $translation): ?string
    {
        if ($contentItem->type !== ContentType::InternalPost) {
            return $translation->external_url;
        }

        return route('blog.show', [
            'locale' => $translation->locale,
            'slug' => $translation->slug,
        ]);
    }

    protected function formatMessage(string $title, string $url): string
    {
        $template = (string) config('social.post_template', ':title — :url');
        $baseMessage = strtr($template, [
            ':title' => $title,
            ':url' => $url,
        ]);

        $hashtags = collect(config('social.hashtags', []))
            ->filter(fn ($tag): bool => is_string($tag) && trim($tag) !== '')
            ->map(function (string $tag): string {
                $normalized = preg_replace('/[^A-Za-z0-9_]/', '', ltrim(trim($tag), '#')) ?? '';

                return $normalized === '' ? '' : '#'.$normalized;
            })
            ->filter()
            ->values()
            ->implode(' ');

        if ($hashtags === '') {
            return $baseMessage;
        }

        return trim($baseMessage.' '.$hashtags);
    }

    protected function shareOnX(ContentItem $contentItem, string $message, string $sharedUrl): bool
    {
        $platform = 'x';

        if ($this->hasSuccessfulShare($contentItem, $platform)) {
            return true;
        }

        $enabled = (bool) config('social.x.enabled', false);
        $token = (string) config('social.x.bearer_token', '');
        $apiUrl = (string) config('social.x.api_url', 'https://api.x.com/2/tweets');
        $payload = ['text' => $message];

        if (! $enabled || $token === '') {
            $this->logAttempt(
                contentItem: $contentItem,
                platform: $platform,
                status: 'skipped',
                sharedUrl: $sharedUrl,
                requestPayload: $payload,
                errorMessage: 'X sharing disabled or missing bearer token.',
            );

            return true;
        }

        $response = Http::timeout(10)
            ->withToken($token)
            ->acceptJson()
            ->post($apiUrl, $payload);

        if ($response->successful()) {
            $this->logAttempt(
                contentItem: $contentItem,
                platform: $platform,
                status: 'success',
                sharedUrl: $sharedUrl,
                requestPayload: $payload,
                responsePayload: $response->json(),
            );

            return true;
        }

        $this->logAttempt(
            contentItem: $contentItem,
            platform: $platform,
            status: 'failed',
            sharedUrl: $sharedUrl,
            requestPayload: $payload,
            responsePayload: ['status' => $response->status(), 'body' => $response->body()],
            errorMessage: 'X API returned an unsuccessful response.',
        );

        return false;
    }

    protected function shareOnLinkedIn(ContentItem $contentItem, string $message, string $sharedUrl): bool
    {
        $platform = 'linkedin';

        if ($this->hasSuccessfulShare($contentItem, $platform)) {
            return true;
        }

        $enabled = (bool) config('social.linkedin.enabled', false);
        $token = (string) config('social.linkedin.access_token', '');
        $authorUrn = (string) config('social.linkedin.author_urn', '');
        $apiUrl = (string) config('social.linkedin.api_url', 'https://api.linkedin.com/v2/ugcPosts');

        $payload = [
            'author' => $authorUrn,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $message,
                    ],
                    'shareMediaCategory' => 'NONE',
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        if (! $enabled || $token === '' || $authorUrn === '') {
            $this->logAttempt(
                contentItem: $contentItem,
                platform: $platform,
                status: 'skipped',
                sharedUrl: $sharedUrl,
                requestPayload: $payload,
                errorMessage: 'LinkedIn sharing disabled or missing credentials.',
            );

            return true;
        }

        $response = Http::timeout(10)
            ->withToken($token)
            ->withHeaders([
                'X-Restli-Protocol-Version' => '2.0.0',
            ])
            ->acceptJson()
            ->post($apiUrl, $payload);

        if ($response->successful()) {
            $this->logAttempt(
                contentItem: $contentItem,
                platform: $platform,
                status: 'success',
                sharedUrl: $sharedUrl,
                requestPayload: $payload,
                responsePayload: $response->json(),
            );

            return true;
        }

        $this->logAttempt(
            contentItem: $contentItem,
            platform: $platform,
            status: 'failed',
            sharedUrl: $sharedUrl,
            requestPayload: $payload,
            responsePayload: ['status' => $response->status(), 'body' => $response->body()],
            errorMessage: 'LinkedIn API returned an unsuccessful response.',
        );

        return false;
    }

    protected function hasSuccessfulShare(ContentItem $contentItem, string $platform): bool
    {
        return $contentItem->socialShareLogs()
            ->where('platform', $platform)
            ->where('status', 'success')
            ->exists();
    }

    /**
     * @param  array<string, mixed>  $requestPayload
     * @param  array<string, mixed>|null  $responsePayload
     */
    protected function logAttempt(
        ContentItem $contentItem,
        string $platform,
        string $status,
        string $sharedUrl,
        array $requestPayload,
        ?array $responsePayload = null,
        ?string $errorMessage = null,
    ): void {
        $contentItem->socialShareLogs()->create([
            'platform' => $platform,
            'status' => $status,
            'shared_url' => Str::limit($sharedUrl, 2048, ''),
            'request_payload' => $requestPayload,
            'response_payload' => $responsePayload,
            'error_message' => $errorMessage,
            'attempted_at' => now(),
        ]);
    }
}
