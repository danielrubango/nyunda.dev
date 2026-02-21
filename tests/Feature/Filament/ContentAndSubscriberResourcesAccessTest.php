<?php

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Enums\SubscriberStatus;
use App\Models\ContentItem;
use App\Models\Subscriber;
use App\Models\User;

test('admin can access subscriber resource list with enum status', function () {
    $admin = User::factory()->admin()->create();
    $subscriber = Subscriber::factory()->create([
        'email' => 'enum-subscriber@test.local',
        'status' => SubscriberStatus::Confirmed->value,
    ]);

    $response = $this->actingAs($admin)->get('/admin/subscribers');

    $response->assertSuccessful();
    $response->assertSee('enum-subscriber@test.local');
    $response->assertSee((string) $subscriber->id);
});

test('admin can access content item resource list with enum columns', function () {
    $admin = User::factory()->admin()->create();
    $contentItem = ContentItem::factory()->create([
        'type' => ContentType::ExternalPost->value,
        'status' => ContentStatus::Published->value,
        'author_id' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->get('/admin/content-items');

    $response->assertSuccessful();
    $response->assertSee((string) $contentItem->id);
});

test('non admin users cannot access subscriber and content item resources', function () {
    $author = User::factory()->author()->create();

    $this->actingAs($author)
        ->get('/admin/subscribers')
        ->assertForbidden();

    $this->actingAs($author)
        ->get('/admin/content-items')
        ->assertForbidden();
});
