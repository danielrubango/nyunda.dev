<?php

use App\Models\User;

test('pending queue page is no longer available for admin users', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin/pending-queue');

    $response->assertNotFound();
});

test('pending queue page is no longer available for non admin users', function () {
    $author = User::factory()->author()->create();

    $response = $this->actingAs($author)->get('/admin/pending-queue');

    $response->assertNotFound();
});
