<?php

namespace App\Http\Controllers\Profiles;

use App\Actions\Seo\BuildSeoMeta;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class ShowPublicProfileController extends Controller
{
    public function __construct(
        private readonly BuildSeoMeta $buildSeoMeta,
    ) {}

    public function __invoke(string $username): View
    {
        $user = User::query()
            ->where('public_profile_slug', $username)
            ->where('is_profile_public', true)
            ->firstOrFail();

        $description = $user->bio
            ?: ($user->headline ?: __('ui.seo.meta.profile', ['name' => $user->name]));

        $sameAs = array_values(array_filter([
            $user->website_url,
            $user->linkedin_url,
            $user->x_url,
            $user->github_url,
        ]));

        return view('profiles.show', [
            'user' => $user,
            'seo' => $this->buildSeoMeta->handle(
                title: $user->name,
                description: $description,
                canonicalUrl: route('profiles.show', ['username' => (string) $user->public_profile_slug]),
                schema: [[
                    '@context' => 'https://schema.org',
                    '@type' => 'ProfilePage',
                    'name' => $user->name,
                    'description' => $description,
                    'url' => route('profiles.show', ['username' => (string) $user->public_profile_slug]),
                    'mainEntity' => array_filter([
                        '@type' => 'Person',
                        'name' => $user->name,
                        'description' => $description,
                        'jobTitle' => $user->headline,
                        'homeLocation' => $user->location,
                        'sameAs' => $sameAs === [] ? null : $sameAs,
                    ], fn (mixed $value): bool => $value !== null),
                ]],
            ),
        ]);
    }
}
