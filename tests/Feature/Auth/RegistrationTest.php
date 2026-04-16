<?php

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertSee('Creer un compte');
    $response->assertSee('Vous avez deja un compte ?');
    $response->assertSee('NYUNDA.DEV');
    $response->assertSee('data-ui-auth-layout', false);
    $response->assertSee('data-ui-auth-card', false);
    $response->assertSee('data-test="oauth-google-button"', false);
    $response->assertSee('data-test="oauth-linkedin-button"', false);
    $response->assertSee(route('oauth.redirect', ['provider' => 'google']), false);
    $response->assertSee(route('oauth.redirect', ['provider' => 'linkedin']), false);
    $response->assertDontSee('id="footer-locale-select"', false);
    $response->assertDontSee(__('ui.nav.blog'));
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
