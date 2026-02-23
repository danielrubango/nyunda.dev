<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('admin users can visit the dashboard', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee(__('ui.nav.blog'));
    $response->assertSee(__('ui.nav.links'));
    $response->assertSee(__('ui.nav.account'));
});

test('non admin users are redirected home with flash status', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('home'));
    $response->assertSessionHas('status', __('ui.flash.connected'));
});
