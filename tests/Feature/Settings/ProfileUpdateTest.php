<?php

use App\Models\User;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Profil')
        ->assertSee('Mettez a jour votre nom et votre adresse e-mail')
        ->assertDontSee('/settings/appearance');
});

test('appearance settings route is not available', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/settings/appearance')
        ->assertNotFound();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('profile about fields and public slug can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Public User')
        ->set('email', $user->email)
        ->set('headline', 'Laravel Developer')
        ->set('bio', 'Je construis des produits web avec Laravel.')
        ->set('location', 'Kigali')
        ->set('website_url', 'https://example.com')
        ->set('linkedin_url', 'https://linkedin.com/in/public-user')
        ->set('x_url', 'https://x.com/public_user')
        ->set('github_url', 'https://github.com/public-user')
        ->set('is_profile_public', true)
        ->set('public_profile_slug', 'public-user-profile')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->headline)->toBe('Laravel Developer')
        ->and($user->bio)->toBe('Je construis des produits web avec Laravel.')
        ->and($user->location)->toBe('Kigali')
        ->and($user->website_url)->toBe('https://example.com')
        ->and($user->linkedin_url)->toBe('https://linkedin.com/in/public-user')
        ->and($user->x_url)->toBe('https://x.com/public_user')
        ->and($user->github_url)->toBe('https://github.com/public-user')
        ->and($user->is_profile_public)->toBeTrue()
        ->and($user->public_profile_slug)->toBe('public-user-profile');
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});
