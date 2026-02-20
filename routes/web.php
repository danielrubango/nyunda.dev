<?php

use App\Http\Controllers\Profiles\ShowPublicProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/@{publicProfileSlug}', ShowPublicProfileController::class)
    ->name('profiles.show');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
