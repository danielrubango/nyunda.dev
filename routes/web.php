<?php

use App\Http\Controllers\Blog\BlogContentController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\LikeContentController;
use App\Http\Controllers\Profiles\ShowPublicProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/@{publicProfileSlug}', ShowPublicProfileController::class)
    ->name('profiles.show');

Route::get('/blog', [BlogContentController::class, 'index'])
    ->name('blog.index');

Route::get('/blog/{locale}/{slug}', [BlogContentController::class, 'show'])
    ->whereIn('locale', config('app.supported_locales', ['fr', 'en']))
    ->name('blog.show');

Route::middleware(['auth'])->group(function () {
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

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
