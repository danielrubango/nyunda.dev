<?php

use App\Actions\Content\DeterminePublishingStatus;
use App\Enums\ContentStatus;
use App\Models\User;

test('users require approval before publication', function () {
    $status = app(DeterminePublishingStatus::class)->handle(
        User::factory()->create(),
    );

    expect($status)->toBe(ContentStatus::Pending);
});

test('authors can publish without approval', function () {
    $status = app(DeterminePublishingStatus::class)->handle(
        User::factory()->author()->create(),
    );

    expect($status)->toBe(ContentStatus::Published);
});

test('admins can publish without approval', function () {
    $status = app(DeterminePublishingStatus::class)->handle(
        User::factory()->admin()->create(),
    );

    expect($status)->toBe(ContentStatus::Published);
});
