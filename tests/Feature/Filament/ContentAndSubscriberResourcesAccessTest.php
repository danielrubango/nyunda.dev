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

test('content item status tabs filter results', function () {
    $admin = User::factory()->admin()->create();
    $publishedAuthor = User::factory()->author()->create([
        'name' => 'Author Published Tab',
    ]);
    $draftAuthor = User::factory()->author()->create([
        'name' => 'Author Draft Tab',
    ]);

    $published = ContentItem::factory()->create([
        'type' => ContentType::InternalPost->value,
        'status' => ContentStatus::Published->value,
        'author_id' => $publishedAuthor->id,
    ]);

    ContentItem::factory()->create([
        'type' => ContentType::InternalPost->value,
        'status' => ContentStatus::Draft->value,
        'author_id' => $draftAuthor->id,
    ]);

    $response = $this->actingAs($admin)->get('/admin/content-items?tab=published');

    $response->assertSuccessful();
    $response->assertSee((string) $published->id);
    $response->assertSee('Author Published Tab');
    $response->assertDontSee('Author Draft Tab');
});

test('subscriber status tabs filter results', function () {
    $admin = User::factory()->admin()->create();

    $pending = Subscriber::factory()->create([
        'email' => 'pending-filter@test.local',
        'status' => SubscriberStatus::Pending->value,
    ]);

    $confirmed = Subscriber::factory()->create([
        'email' => 'confirmed-filter@test.local',
        'status' => SubscriberStatus::Confirmed->value,
    ]);

    $response = $this->actingAs($admin)->get('/admin/subscribers?tab=pending');

    $response->assertSuccessful();
    $response->assertSee($pending->email);
    $response->assertDontSee($confirmed->email);
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
