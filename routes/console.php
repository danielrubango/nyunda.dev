<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('content-reads:prune')
    ->dailyAt('03:00')
    ->withoutOverlapping();

Schedule::command('content-items:publish-scheduled')
    ->everyMinute()
    ->withoutOverlapping();

// Chaque 1er du mois à 08h : prépare un brouillon newsletter avec les articles du mois écoulé
Schedule::command('newsletter:prepare-monthly')
    ->monthlyOn(1, '08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/newsletter.log'));
