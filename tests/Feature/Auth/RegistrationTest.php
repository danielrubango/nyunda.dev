<?php

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertSee('Creer un compte');
    $response->assertSee('Vous avez deja un compte ?');
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
});

test('registration screen shows configured social providers and separator', function () {
    config([
        'services.google.enabled' => true,
        'services.linkedin-openid.enabled' => true,
    ]);

    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertSee('data-test="oauth-buttons"', false);
    $response->assertSee('data-test="oauth-google-button"', false);
    $response->assertSee('data-test="oauth-linkedin-button"', false);
    $response->assertSee(route('oauth.redirect', ['provider' => 'google']), false);
    $response->assertSee(route('oauth.redirect', ['provider' => 'linkedin']), false);
    $response->assertSee(__('ui.auth.social.heading'));
    $response->assertSee('data-test="auth-methods-separator"', false);
    $response->assertSee(__('ui.auth.social.separator'));
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('home', absolute: false));

    $this->assertAuthenticated();
});
