<?php

use App\Models\User;

test('registration validation errors are displayed in french', function () {
    app()->setLocale('fr');

    $response = $this->post(route('register.store'), [
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'short',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors([
        'name' => 'Le champ nom est obligatoire.',
        'email' => 'Le champ adresse e-mail doit etre une adresse e-mail valide.',
        'password' => 'La confirmation du champ mot de passe ne correspond pas.',
    ]);
});

test('login failure message is displayed in french', function () {
    app()->setLocale('fr');

    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors([
        'email' => __('auth.failed'),
    ]);
});

test('password reset invalid token message is displayed in french', function () {
    app()->setLocale('fr');

    $user = User::factory()->create();

    $response = $this->post(route('password.update'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'email' => __('passwords.token'),
    ]);
});
