<?php

use App\Enums\ContentStatus;
use App\Models\ContentItem;
use App\Models\User;

test('admin can access pending queue page', function () {
    $admin = User::factory()->admin()->create();
    $pendingItem = ContentItem::factory()->create([
        'status' => ContentStatus::Pending->value,
    ]);

    $response = $this->actingAs($admin)->get('/admin/pending-queue');

    $response->assertSuccessful();
    $response->assertSee((string) $pendingItem->id);
});

test('non admin users cannot access pending queue page', function () {
    $author = User::factory()->author()->create();

    $response = $this->actingAs($author)->get('/admin/pending-queue');

    $response->assertForbidden();
});
