<?php

use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\Admin\ExportSubscribersCsvController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Blog\BlogContentController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\CommunityLinkSubmissionsController;
use App\Http\Controllers\ConfirmNewsletterController;
use App\Http\Controllers\Dashboard\UserContentController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\LikeContentController;
use App\Http\Controllers\LinksPageController;
use App\Http\Controllers\NewsletterSubscriptionsController;
use App\Http\Controllers\Profiles\ShowPublicProfileController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UnsubscribeNewsletterController;
use App\Http\Controllers\UpdateLocaleController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePageController::class)->name('home');

Route::get('/sitemap.xml', SitemapController::class)
    ->name('seo.sitemap');

Route::get('/feed.xml', RssFeedController::class)
    ->name('seo.feed');

Route::post('/locale', UpdateLocaleController::class)
    ->name('locale.update');

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->whereIn('provider', ['google', 'linkedin'])
    ->name('oauth.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->whereIn('provider', ['google', 'linkedin'])
    ->name('oauth.callback');

Route::get('/u/{username}', ShowPublicProfileController::class)
    ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]{2,39}')
    ->name('profiles.show');

Route::get('/about', AboutPageController::class)
    ->name('about.show');

Route::get('/links', LinksPageController::class)
    ->name('links.index');

Route::get('/blog', [BlogContentController::class, 'index'])
    ->name('blog.index');

Route::get('/blog/{slug}', [BlogContentController::class, 'showBySlug'])
    ->name('blog.show.localized');

Route::get('/blog/{locale}/{slug}', [BlogContentController::class, 'show'])
    ->whereIn('locale', config('app.supported_locales', ['fr', 'en']))
    ->name('blog.show');

Route::post('/newsletter/subscriptions', [NewsletterSubscriptionsController::class, 'store'])
    ->middleware('throttle:newsletter-subscriptions')
    ->name('newsletter.subscriptions.store');

Route::get('/newsletter/confirm/{token}', ConfirmNewsletterController::class)
    ->name('newsletter.confirm');

Route::get('/newsletter/unsubscribe/{token}', UnsubscribeNewsletterController::class)
    ->name('newsletter.unsubscribe');

$redirectForumUnavailable = static function (): RedirectResponse {
    return redirect()
        ->back(fallback: route('home'))
        ->with('status', __('ui.flash.forum_coming_soon'));
};

Route::get('/forum', $redirectForumUnavailable)
    ->name('forum.index');

Route::middleware(['auth', 'seo.noindex'])->group(function () use ($redirectForumUnavailable): void {
    Route::get('/forum/create', $redirectForumUnavailable)
        ->name('forum.create');

    Route::post('/forum', $redirectForumUnavailable)
        ->name('forum.store');

    Route::get('/forum/{forumThread}/edit', $redirectForumUnavailable)
        ->name('forum.edit');

    Route::put('/forum/{forumThread}', $redirectForumUnavailable)
        ->name('forum.update');

    Route::delete('/forum/{forumThread}', $redirectForumUnavailable)
        ->name('forum.destroy');

    Route::patch('/forum/{forumThread}/visibility', $redirectForumUnavailable)
        ->name('forum.visibility.update');

    Route::post('/forum/{forumThread}/replies', $redirectForumUnavailable)
        ->name('forum.replies.store');

    Route::post('/forum/{forumThread}/replies/{forumReply}/best', $redirectForumUnavailable)
        ->name('forum.replies.mark-best');

    Route::patch('/forum/replies/{forumReply}', $redirectForumUnavailable)
        ->name('forum.replies.update');

    Route::delete('/forum/replies/{forumReply}', $redirectForumUnavailable)
        ->name('forum.replies.destroy');
});

Route::get('/forum/{forumThread}', $redirectForumUnavailable)
    ->name('forum.show');

Route::middleware(['auth', 'seo.noindex'])->group(function () {
    Route::get('/admin/subscribers/export', ExportSubscribersCsvController::class)
        ->name('admin.subscribers.export');

    Route::get('/community-links/create', [CommunityLinkSubmissionsController::class, 'create'])
        ->name('community-links.create');

    Route::post('/community-links', [CommunityLinkSubmissionsController::class, 'store'])
        ->middleware('throttle:community-submissions')
        ->name('community-links.store');

    Route::post('/content/{contentItem}/comments', [CommentsController::class, 'store'])
        ->middleware('throttle:content-comments')
        ->name('content.comments.store');

    Route::post('/content/{contentItem}/likes', LikeContentController::class)
        ->middleware('throttle:content-likes')
        ->name('content.likes.toggle');

    Route::patch('/comments/{comment}', [CommentsController::class, 'update'])
        ->name('comments.update');

    Route::delete('/comments/{comment}', [CommentsController::class, 'destroy'])
        ->name('comments.destroy');
});

Route::get('dashboard', function () {
    return view('dashboard');
})
    ->middleware(['auth', 'verified', 'admin.only', 'seo.noindex'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'seo.noindex'])->group(function (): void {
    Route::get('/dashboard/content', [UserContentController::class, 'index'])
        ->name('dashboard.content.index');

    Route::get('/dashboard/content/create', [UserContentController::class, 'create'])
        ->name('dashboard.content.create');

    Route::post('/dashboard/content', [UserContentController::class, 'store'])
        ->name('dashboard.content.store');

    Route::get('/dashboard/content/{contentItem}/edit', [UserContentController::class, 'edit'])
        ->name('dashboard.content.edit');

    Route::put('/dashboard/content/{contentItem}', [UserContentController::class, 'update'])
        ->name('dashboard.content.update');

    Route::get('/dashboard/activity/comments', function (): RedirectResponse {
        return redirect()->route('dashboard.content.index');
    })->name('dashboard.activity.comments');
});

Route::view('/style-guide', 'style-guide')
    ->name('style-guide');

require __DIR__.'/settings.php';
