<?php

namespace App\Http\Controllers;

use App\Actions\Seo\BuildSeoMeta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function __construct(
        private readonly BuildSeoMeta $buildSeoMeta,
    ) {}

    public function __invoke(Request $request): View
    {
        $preferredLocale = $request->user()?->preferred_locale;

        if (! is_string($preferredLocale) || $preferredLocale === '') {
            $preferredLocale = $request->getPreferredLanguage(config('app.supported_locales', ['fr', 'en']));
        }

        if (is_string($preferredLocale) && $preferredLocale !== '') {
            app()->setLocale($preferredLocale);
        }

        return view('about', [
            'seo' => $this->buildSeoMeta->handle(
                title: __('ui.about.title'),
                description: __('ui.about.summary_text'),
                canonicalUrl: route('about.show'),
            ),
        ]);
    }
}
