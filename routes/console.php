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
