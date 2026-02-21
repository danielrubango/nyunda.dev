<?php

use App\Enums\UserRole;
use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\Admin\ExportSubscribersCsvController;
use App\Http\Controllers\Blog\BlogContentController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\CommunityLinkSubmissionsController;
use App\Http\Controllers\ConfirmNewsletterController;
use App\Http\Controllers\Forum\ForumRepliesController;
use App\Http\Controllers\Forum\ForumThreadsController;
use App\Http\Controllers\Forum\MarkBestForumReplyController;
use App\Http\Controllers\Forum\UpdateForumThreadVisibilityController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\LikeContentController;
use App\Http\Controllers\LinksPageController;
use App\Http\Controllers\NewsletterSubscriptionsController;
use App\Http\Controllers\Profiles\ShowPublicProfileController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UnsubscribeNewsletterController;
use App\Http\Controllers\UpdateLocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePageController::class)->name('home');

Route::get('/sitemap.xml', SitemapController::class)
    ->name('seo.sitemap');

Route::get('/feed.xml', RssFeedController::class)
    ->name('seo.feed');

Route::post('/locale', UpdateLocaleController::class)
    ->name('locale.update');

Route::get('/u/{username}', ShowPublicProfileController::class)
    ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]{2,39}')
    ->name('profiles.show');

Route::get('/about', AboutPageController::class)
    ->name('about.show');

Route::get('/links', LinksPageController::class)
    ->name('links.index');

Route::get('/forum', [ForumThreadsController::class, 'index'])
    ->name('forum.index');

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

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/subscribers/export', ExportSubscribersCsvController::class)
        ->name('admin.subscribers.export');

    Route::get('/community-links/create', [CommunityLinkSubmissionsController::class, 'create'])
        ->name('community-links.create');

    Route::post('/community-links', [CommunityLinkSubmissionsController::class, 'store'])
        ->middleware('throttle:community-submissions')
        ->name('community-links.store');

    Route::get('/forum/create', [ForumThreadsController::class, 'create'])
        ->name('forum.create');

    Route::post('/forum', [ForumThreadsController::class, 'store'])
        ->middleware('throttle:forum-threads')
        ->name('forum.store');

    Route::get('/forum/{forumThread:slug}/edit', [ForumThreadsController::class, 'edit'])
        ->name('forum.edit');

    Route::put('/forum/{forumThread:slug}', [ForumThreadsController::class, 'update'])
        ->name('forum.update');

    Route::delete('/forum/{forumThread:slug}', [ForumThreadsController::class, 'destroy'])
        ->name('forum.destroy');

    Route::patch('/forum/{forumThread:slug}/visibility', UpdateForumThreadVisibilityController::class)
        ->name('forum.visibility.update');

    Route::post('/forum/{forumThread:slug}/replies', [ForumRepliesController::class, 'store'])
        ->middleware('throttle:forum-replies')
        ->name('forum.replies.store');

    Route::post('/forum/{forumThread:slug}/replies/{forumReply}/best', MarkBestForumReplyController::class)
        ->name('forum.replies.mark-best');

    Route::patch('/forum/replies/{forumReply}', [ForumRepliesController::class, 'update'])
        ->name('forum.replies.update');

    Route::delete('/forum/replies/{forumReply}', [ForumRepliesController::class, 'destroy'])
        ->name('forum.replies.destroy');

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

Route::get('/forum/{forumThread:slug}', [ForumThreadsController::class, 'show'])
    ->name('forum.show');

Route::get('dashboard', function () {
    $user = auth()->user();

    if (! $user || ! $user->hasRole(UserRole::Admin)) {
        return redirect()
            ->route('home')
            ->with('status', __('ui.flash.connected'));
    }

    return view('dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('/style-guide', 'style-guide')
    ->name('style-guide');

require __DIR__.'/settings.php';
