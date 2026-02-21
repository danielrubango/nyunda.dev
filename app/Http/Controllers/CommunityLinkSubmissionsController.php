<?php

namespace App\Http\Controllers;

use App\Actions\Content\SubmitCommunityLink;
use App\Actions\Seo\BuildSeoMeta;
use App\Http\Requests\Community\StoreCommunityLinkSubmissionRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommunityLinkSubmissionsController extends Controller
{
    public function __construct(
        private readonly BuildSeoMeta $buildSeoMeta,
    ) {}

    public function create(): View
    {
        $userLocale = $this->resolveUserLocale();
        app()->setLocale($userLocale);

        return view('community-links.create', [
            'supportedLocales' => config('app.supported_locales', ['fr', 'en']),
            'defaultLocale' => $userLocale,
            'seo' => $this->buildSeoMeta->handle(
                title: __('ui.blog.submit_community_link'),
                description: __('ui.community.description'),
                canonicalUrl: route('community-links.create'),
            ),
        ]);
    }

    public function store(
        StoreCommunityLinkSubmissionRequest $request,
        SubmitCommunityLink $submitCommunityLink,
    ): RedirectResponse {
        /** @var User $author */
        $author = $request->user();

        $submitCommunityLink->handle(
            author: $author,
            submission: $request->submissionData(),
        );

        return redirect()
            ->route('blog.index')
            ->with('status', __('ui.community.status.submitted'));
    }

    protected function resolveUserLocale(): string
    {
        /** @var User|null $user */
        $user = auth()->user();
        $userLocale = $user?->preferred_locale;

        if (is_string($userLocale) && in_array($userLocale, config('app.supported_locales', ['fr', 'en']), true)) {
            return $userLocale;
        }

        return (string) config('app.locale', 'fr');
    }
}
