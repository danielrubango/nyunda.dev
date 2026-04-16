<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('Connectez-vous a votre compte');
    $response->assertSee('Connexion');
    $response->assertSee('NYUNDA.DEV');
    $response->assertSee('data-ui-auth-layout', false);
    $response->assertSee('data-ui-auth-card', false);
    $response->assertDontSee('data-test="oauth-buttons"', false);
    $response->assertDontSee('data-test="oauth-google-button"', false);
    $response->assertDontSee('data-test="oauth-linkedin-button"', false);
    $response->assertDontSee('data-test="auth-methods-separator"', false);
    $response->assertDontSee(route('oauth.redirect', ['provider' => 'google']), false);
    $response->assertDontSee(route('oauth.redirect', ['provider' => 'linkedin']), false);
    $response->assertDontSee('id="footer-locale-select"', false);
    $response->assertDontSee(__('ui.nav.blog'));
    $response->assertSee('<meta name="robots" content="noindex,follow">', false);
    $response->assertSee('<meta name="description" content="'.e(__('Enter your email and password below to log in')).'">', false);
});

test('login screen shows only configured social providers and separator', function () {
    config([
        'services.google.enabled' => true,
        'services.linkedin-openid.enabled' => false,
    ]);

    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('data-test="oauth-buttons"', false);
    $response->assertSee('data-test="oauth-google-button"', false);
    $response->assertDontSee('data-test="oauth-linkedin-button"', false);
    $response->assertSee(route('oauth.redirect', ['provider' => 'google']), false);
    $response->assertDontSee(route('oauth.redirect', ['provider' => 'linkedin']), false);
    $response->assertSee(__('ui.auth.social.heading'));
    $response->assertSee('data-test="auth-methods-separator"', false);
    $response->assertSee(__('ui.auth.social.separator'));
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});
