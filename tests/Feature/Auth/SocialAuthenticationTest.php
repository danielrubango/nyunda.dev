<?php

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

test('oauth redirect uses expected socialite driver', function () {
    $provider = \Mockery::mock();
    $provider->shouldReceive('redirect')
        ->once()
        ->andReturn(redirect('https://oauth.test/google'));

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($provider);

    $response = $this->get(route('oauth.redirect', ['provider' => 'google']));

    $response->assertRedirect('https://oauth.test/google');
});

test('linkedin oauth redirect maps to linkedin openid driver', function () {
    $provider = \Mockery::mock();
    $provider->shouldReceive('redirect')
        ->once()
        ->andReturn(redirect('https://oauth.test/linkedin'));

    Socialite::shouldReceive('driver')
        ->once()
        ->with('linkedin-openid')
        ->andReturn($provider);

    $response = $this->get(route('oauth.redirect', ['provider' => 'linkedin']));

    $response->assertRedirect('https://oauth.test/linkedin');
});

test('oauth callback links provider to existing user by email and authenticates', function () {
    $existingUser = User::factory()->create([
        'email' => 'jane@example.com',
    ]);

    $provider = \Mockery::mock();
    $provider->shouldReceive('user')
        ->once()
        ->andReturn(fakeSocialiteUser(
            id: 'google-123',
            email: 'jane@example.com',
            name: 'Jane Doe',
            avatar: 'https://avatars.test/jane.png',
        ));

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($provider);

    $response = $this->get(route('oauth.callback', ['provider' => 'google']));

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($existingUser);

    $this->assertDatabaseHas('social_accounts', [
        'user_id' => $existingUser->id,
        'provider' => 'google',
        'provider_user_id' => 'google-123',
        'provider_email' => 'jane@example.com',
    ]);
});

test('oauth callback creates a new verified user when no matching account exists', function () {
    $this->assertGuest();
    expect(User::query()->count())->toBe(0);

    $provider = \Mockery::mock();
    $provider->shouldReceive('user')
        ->once()
        ->andReturn(fakeSocialiteUser(
            id: 'linkedin-999',
            email: 'new-user@example.com',
            name: 'New LinkedIn User',
            avatar: 'https://avatars.test/new-user.png',
        ));

    Socialite::shouldReceive('driver')
        ->once()
        ->with('linkedin-openid')
        ->andReturn($provider);

    $response = $this->get(route('oauth.callback', ['provider' => 'linkedin']));

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();

    $createdUser = User::query()->first();

    expect($createdUser)->not->toBeNull()
        ->and($createdUser->email_verified_at)->not->toBeNull()
        ->and(User::query()->count())->toBe(1);

    $this->assertDatabaseHas('social_accounts', [
        'user_id' => $createdUser->id,
        'provider' => 'linkedin',
        'provider_user_id' => 'linkedin-999',
    ]);
});

test('oauth callback redirects back to login on provider failure', function () {
    $provider = \Mockery::mock();
    $provider->shouldReceive('user')
        ->once()
        ->andThrow(new RuntimeException('oauth failure'));

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($provider);

    $response = $this->get(route('oauth.callback', ['provider' => 'google']));

    $response->assertRedirect(route('login', absolute: false));
    $response->assertSessionHas('oauth_error');
    $this->get(route('login'))
        ->assertSee(__('ui.auth.social.callback_error'));
    $this->assertGuest();
});

function fakeSocialiteUser(string $id, ?string $email, ?string $name, ?string $avatar): SocialiteUser
{
    return new class($id, $email, $name, $avatar) implements SocialiteUser
    {
        public function __construct(
            private readonly string $id,
            private readonly ?string $email,
            private readonly ?string $name,
            private readonly ?string $avatar,
        ) {}

        public function getId()
        {
            return $this->id;
        }

        public function getNickname()
        {
            return null;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function getAvatar()
        {
            return $this->avatar;
        }
    };
}
