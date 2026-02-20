<?php

use App\Models\User;
use Illuminate\Support\Str;

test('private profiles are not publicly accessible', function () {
    $slug = Str::slug(fake()->name());
    User::factory()->create([
        'public_profile_slug' => $slug,
        'is_profile_public' => false,
    ]);

    $response = $this->get('/@'.$slug);

    $response->assertNotFound();
});

test('public profiles are accessible', function () {
    $user = User::factory()->withPublicProfile('john-doe')->create([
        'name' => 'John Doe',
    ]);

    $response = $this->get('/@'.$user->public_profile_slug);

    $response
        ->assertOk()
        ->assertSee('John Doe');
});
