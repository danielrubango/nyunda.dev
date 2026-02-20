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

        return view('profiles.show', [
            'user' => $user,
            'seo' => $this->buildSeoMeta->handle(
                title: $user->name,
                description: 'Profil public de '.$user->name.' sur '.config('app.name').'.',
                canonicalUrl: route('profiles.show', ['username' => (string) $user->public_profile_slug]),
            ),
        ]);
    }
}
