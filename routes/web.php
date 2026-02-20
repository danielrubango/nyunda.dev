<?php

use App\Http\Controllers\Blog\BlogContentController;
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

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
