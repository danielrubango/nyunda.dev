<?php

namespace App\Http\Controllers;

use App\Actions\Seo\BuildRssFeedItems;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RssFeedController extends Controller
{
    public function __construct(
        private readonly BuildRssFeedItems $buildRssFeedItems,
    ) {}

    public function __invoke(Request $request): Response
    {
        return response()
            ->view('seo.feed', [
                'feedItems' => $this->buildRssFeedItems->handle($request->getPreferredLanguage(
                    config('app.supported_locales', ['fr', 'en']),
                )),
            ])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
