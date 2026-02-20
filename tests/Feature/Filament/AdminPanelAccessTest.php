<?php

use App\Models\User;

test('guest is redirected to admin login page', function () {
    $response = $this->get('/admin');

    $response->assertRedirect('/admin/login');
});

test('admin can access admin panel', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertSuccessful();
});

test('non admin users cannot access admin panel', function () {
    $author = User::factory()->author()->create();

    $response = $this->actingAs($author)->get('/admin');

    $response->assertForbidden();
});
