<?php

use App\Enums\UserRole;
use App\Models\User;

test('new users have expected foundation defaults', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(UserRole::User)
        ->and($user->preferred_locale)->toBe('fr')
        ->and($user->is_profile_public)->toBeFalse()
        ->and($user->public_profile_slug)->toBeNull();
});

test('only authors and admins can publish without approval', function () {
    $user = User::factory()->create();
    $author = User::factory()->author()->create();
    $admin = User::factory()->admin()->create();

    expect($user->canPublishWithoutApproval())->toBeFalse()
        ->and($author->canPublishWithoutApproval())->toBeTrue()
        ->and($admin->canPublishWithoutApproval())->toBeTrue();
});
