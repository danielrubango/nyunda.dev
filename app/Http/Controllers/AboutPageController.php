<?php

namespace App\Http\Controllers;

use App\Actions\Seo\BuildSeoMeta;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function __construct(
        private readonly BuildSeoMeta $buildSeoMeta,
    ) {}

    public function __invoke(): View
    {
        return view('about', [
            'seo' => $this->buildSeoMeta->handle(
                title: 'About',
                description: 'Presentation, parcours et competences autour de Laravel, PHP et architecture logicielle.',
                canonicalUrl: route('about.show'),
            ),
        ]);
    }
}
