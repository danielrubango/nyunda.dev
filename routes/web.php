<?php

use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\Admin\ExportSubscribersCsvController;
use App\Http\Controllers\Blog\BlogContentController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\CommunityLinkSubmissionsController;
use App\Http\Controllers\ConfirmNewsletterController;
use App\Http\Controllers\Forum\ForumRepliesController;
use App\Http\Controllers\Forum\ForumThreadsController;
use App\Http\Controllers\Forum\MarkBestForumReplyController;
use App\Http\Controllers\LikeContentController;
use App\Http\Controllers\NewsletterSubscriptionsController;
use App\Http\Controllers\Profiles\ShowPublicProfileController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UnsubscribeNewsletterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/sitemap.xml', SitemapController::class)
    ->name('seo.sitemap');

Route::get('/feed.xml', RssFeedController::class)
    ->name('seo.feed');

Route::get('/u/{username}', ShowPublicProfileController::class)
    ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]{2,39}')
    ->name('profiles.show');

Route::get('/about', AboutPageController::class)
    ->name('about.show');

Route::get('/forum', [ForumThreadsController::class, 'index'])
    ->name('forum.index');

Route::get('/blog', [BlogContentController::class, 'index'])
    ->name('blog.index');

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

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
