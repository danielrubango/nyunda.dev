<?php

use App\Models\Subscriber;
use App\Models\User;

test('admin can export subscribers as csv', function () {
    $admin = User::factory()->admin()->create();

    Subscriber::factory()->create([
        'email' => 'alpha@example.com',
        'locale' => 'fr',
    ]);
    Subscriber::factory()->confirmed()->create([
        'email' => 'beta@example.com',
        'locale' => 'en',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.subscribers.export'));

    $response->assertSuccessful();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $csv = $response->streamedContent();
    expect($csv)->toContain('email,status,locale,confirmed_at,created_at')
        ->toContain('alpha@example.com')
        ->toContain('beta@example.com');
});

test('non admin users cannot export subscribers csv', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.subscribers.export'));

    $response->assertForbidden();
});
