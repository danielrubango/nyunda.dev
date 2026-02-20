<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class ShowPublicProfileController extends Controller
{
    public function __invoke(string $publicProfileSlug): View
    {
        $user = User::query()
            ->where('public_profile_slug', $publicProfileSlug)
            ->where('is_profile_public', true)
            ->firstOrFail();

        return view('profiles.show', [
            'user' => $user,
        ]);
    }
}
