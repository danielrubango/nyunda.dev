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
        return view('about', [
            'socialLinks' => [
                [
                    'label' => 'LinkedIn',
                    'icon' => 'linkedin',
                    'url' => 'https://www.linkedin.com/in/danielrubango/',
                ],
                [
                    'label' => 'GitHub',
                    'icon' => 'github',
                    'url' => 'https://github.com/danielrubango',
                ],
            ],
            'seo' => $this->buildSeoMeta->handle(
                title: __('ui.about.title'),
                description: __('ui.about.intro'),
                canonicalUrl: route('about.show'),
            ),
        ]);
    }
}
