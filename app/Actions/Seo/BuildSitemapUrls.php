<?php

namespace App\Actions\Seo;

use App\Enums\ContentType;
use App\Models\ContentTranslation;
use App\Models\ForumThread;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class BuildSitemapUrls
{
    /**
     * @return Collection<int, array{
     *     loc: string,
     *     lastmod: CarbonInterface|null,
     *     changefreq: string,
     *     priority: string
     * }>
     */
    public function handle(): Collection
    {
        $staticUrls = collect([
            [
                'loc' => route('home'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '1.00',
            ],
            [
                'loc' => route('blog.index'),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.90',
            ],
            [
                'loc' => route('about.show'),
                'lastmod' => null,
                'changefreq' => 'monthly',
                'priority' => '0.70',
            ],
            [
                'loc' => route('links.index'),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.75',
            ],
            [
                'loc' => route('forum.index'),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.80',
            ],
            [
                'loc' => route('seo.feed'),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.60',
            ],
        ]);

        $contentUrls = ContentTranslation::query()
            ->select(['id', 'content_item_id', 'locale', 'slug', 'updated_at'])
            ->whereHas('contentItem', function ($query): void {
                $query
                    ->published()
                    ->where('type', ContentType::InternalPost->value);
            })
            ->with([
                'contentItem:id,published_at,updated_at',
            ])
            ->orderByDesc('id')
            ->get()
            ->map(function (ContentTranslation $translation): array {
                $lastModifiedAt = $translation->contentItem->published_at
                    ?? $translation->updated_at
                    ?? $translation->contentItem->updated_at;

                return [
                    'loc' => route('blog.show', [
                        'locale' => $translation->locale,
                        'slug' => $translation->slug,
                    ]),
                    'lastmod' => $lastModifiedAt,
                    'changefreq' => 'weekly',
                    'priority' => '0.80',
                ];
            });

        $profileUrls = User::query()
            ->where('is_profile_public', true)
            ->whereNotNull('public_profile_slug')
            ->select(['public_profile_slug', 'updated_at'])
            ->get()
            ->map(function (User $user): array {
                return [
                    'loc' => route('profiles.show', [
                        'username' => (string) $user->public_profile_slug,
                    ]),
                    'lastmod' => $user->updated_at,
                    'changefreq' => 'monthly',
                    'priority' => '0.50',
                ];
            });

        $forumUrls = ForumThread::query()
            ->where('is_hidden', false)
            ->select(['slug', 'updated_at'])
            ->get()
            ->map(function (ForumThread $forumThread): array {
                return [
                    'loc' => route('forum.show', $forumThread),
                    'lastmod' => $forumThread->updated_at,
                    'changefreq' => 'weekly',
                    'priority' => '0.70',
                ];
            });

        return $staticUrls
            ->concat($contentUrls)
            ->concat($forumUrls)
            ->concat($profileUrls)
            ->values();
    }
}
