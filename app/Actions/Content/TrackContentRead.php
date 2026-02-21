<?php

namespace App\Actions\Content;

use App\Models\ContentItem;
use App\Models\ContentRead;
use App\Models\User;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class TrackContentRead
{
    public const string VISITOR_COOKIE_NAME = 'nyunda_vid';

    /**
     * @return array{counted: bool, queuedVisitorCookie: bool}
     */
    public function handle(Request $request, ContentItem $contentItem): array
    {
        if (! $contentItem->isInternalPost() || ! $contentItem->isPublished()) {
            return [
                'counted' => false,
                'queuedVisitorCookie' => false,
            ];
        }

        $user = $request->user();

        if ($user instanceof User) {
            $counted = $this->trackForAuthenticatedUser(
                contentItem: $contentItem,
                user: $user,
            );

            return [
                'counted' => $counted,
                'queuedVisitorCookie' => false,
            ];
        }

        [$visitorFingerprint, $queuedVisitorCookie] = $this->resolveGuestFingerprint($request);

        if ($visitorFingerprint === null) {
            return [
                'counted' => false,
                'queuedVisitorCookie' => $queuedVisitorCookie,
            ];
        }

        $counted = $this->trackForGuest(
            request: $request,
            contentItem: $contentItem,
            visitorFingerprint: $visitorFingerprint,
        );

        return [
            'counted' => $counted,
            'queuedVisitorCookie' => $queuedVisitorCookie,
        ];
    }

    protected function trackForAuthenticatedUser(ContentItem $contentItem, User $user): bool
    {
        $windowStart = now()->subHour();
        $lockKey = sprintf('content-read:%d:user:%d', $contentItem->id, $user->id);

        return $this->withLock($lockKey, function () use ($contentItem, $user, $windowStart): bool {
            $alreadyCounted = ContentRead::query()
                ->where('content_item_id', $contentItem->id)
                ->where('user_id', $user->id)
                ->where('counted_at', '>', $windowStart)
                ->exists();

            if ($alreadyCounted) {
                return false;
            }

            ContentRead::query()->create([
                'content_item_id' => $contentItem->id,
                'user_id' => $user->id,
                'visitor_fingerprint' => null,
                'counted_at' => now(),
            ]);

            $contentItem->increment('reads_count');

            return true;
        });
    }

    protected function trackForGuest(Request $request, ContentItem $contentItem, string $visitorFingerprint): bool
    {
        $windowStart = now()->subHours(4);
        $fallbackFingerprint = $this->resolveFallbackFingerprint($request);
        $lockKey = sprintf('content-read:%d:guest:%s', $contentItem->id, $visitorFingerprint);

        return $this->withLock($lockKey, function () use ($contentItem, $visitorFingerprint, $fallbackFingerprint, $windowStart): bool {
            $alreadyCounted = ContentRead::query()
                ->where('content_item_id', $contentItem->id)
                ->where('counted_at', '>', $windowStart)
                ->where(function ($query) use ($visitorFingerprint, $fallbackFingerprint): void {
                    $query->where('visitor_fingerprint', $visitorFingerprint);

                    if (is_string($fallbackFingerprint) && $fallbackFingerprint !== $visitorFingerprint) {
                        $query->orWhere('visitor_fingerprint', $fallbackFingerprint);
                    }
                })
                ->exists();

            if ($alreadyCounted) {
                return false;
            }

            ContentRead::query()->create([
                'content_item_id' => $contentItem->id,
                'user_id' => null,
                'visitor_fingerprint' => $visitorFingerprint,
                'counted_at' => now(),
            ]);

            $contentItem->increment('reads_count');

            return true;
        });
    }

    /**
     * @return array{0: ?string, 1: bool}
     */
    protected function resolveGuestFingerprint(Request $request): array
    {
        $cookieValue = $request->cookie(self::VISITOR_COOKIE_NAME);

        if (is_string($cookieValue) && $cookieValue !== '') {
            return [
                hash('sha256', 'cookie:'.$cookieValue),
                false,
            ];
        }

        $fallbackFingerprint = $this->resolveFallbackFingerprint($request);
        $newCookieValue = (string) Str::uuid();

        Cookie::queue(cookie()->make(
            name: self::VISITOR_COOKIE_NAME,
            value: $newCookieValue,
            minutes: 60 * 24 * 365,
            path: '/',
            secure: $request->isSecure(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        ));

        if (is_string($fallbackFingerprint)) {
            return [
                $fallbackFingerprint,
                true,
            ];
        }

        return [
            hash('sha256', 'cookie:'.$newCookieValue),
            true,
        ];
    }

    protected function resolveFallbackFingerprint(Request $request): ?string
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        if (! is_string($ipAddress) || $ipAddress === '') {
            return null;
        }

        if (! is_string($userAgent) || $userAgent === '') {
            return null;
        }

        return hash('sha256', 'fallback:'.$ipAddress.'|'.$userAgent);
    }

    protected function withLock(string $key, callable $callback): bool
    {
        $lock = Cache::lock($key, 5);

        try {
            $lock->block(2);
        } catch (LockTimeoutException) {
            return false;
        }

        try {
            return (bool) $callback();
        } finally {
            $lock->release();
        }
    }
}
