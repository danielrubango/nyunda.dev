<?php

namespace App\Http\Controllers;

use App\Actions\Seo\BuildSeoMeta;
use App\Models\Project;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function __construct(
        private readonly BuildSeoMeta $buildSeoMeta,
    ) {}

    public function __invoke(Request $request): View
    {
        $projects = Project::query()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'url']);

        $tools = Tool::query()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'url']);

        return view('about', [
            'projects' => $projects,
            'tools' => $tools,
            'socialLinks' => [
                [
                    'label' => 'LinkedIn',
                    'url' => 'https://www.linkedin.com/in/your-profile',
                ],
                [
                    'label' => 'GitHub',
                    'url' => 'https://github.com',
                ],
            ],
            'seo' => $this->buildSeoMeta->handle(
                title: __('ui.about.title'),
                description: __('ui.about.summary_text'),
                canonicalUrl: route('about.show'),
            ),
        ]);
    }
}
