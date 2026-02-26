<?php

use App\Models\User;
use Illuminate\Support\Str;

test('private profiles are not publicly accessible', function () {
    $slug = Str::slug(fake()->name());
    User::factory()->create([
        'public_profile_slug' => $slug,
        'is_profile_public' => false,
    ]);

    $response = $this->get('/u/'.$slug);

    $response->assertNotFound();
});

test('public profiles are accessible', function () {
    $user = User::factory()->withPublicProfile('john-doe')->create([
        'name' => 'John Doe',
        'headline' => 'Full Stack Developer',
        'bio' => 'Je partage des contenus techniques.',
        'location' => 'Kigali',
        'website_url' => 'https://example.com',
        'linkedin_url' => 'https://linkedin.com/in/john-doe',
    ]);

    $response = $this->get('/u/'.$user->public_profile_slug);

    $response
        ->assertOk()
        ->assertSee('John Doe')
        ->assertSee('Full Stack Developer')
        ->assertSee('Je partage des contenus techniques.')
        ->assertSee('Kigali')
        ->assertSee('https://example.com')
        ->assertSee('https://linkedin.com/in/john-doe');
});
