<?php

namespace App\Http\Controllers;

use App\Actions\Seo\BuildSitemapUrls;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __construct(
        private readonly BuildSitemapUrls $buildSitemapUrls,
    ) {}

    public function __invoke(): Response
    {
        return response()
            ->view('seo.sitemap', [
                'urls' => $this->buildSitemapUrls->handle(),
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
