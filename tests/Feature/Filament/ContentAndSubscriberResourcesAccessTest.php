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
});

test('admin can access editorial typed resources', function () {
    $admin = User::factory()->admin()->create();

    $internalPost = ContentItem::factory()->create([
        'type' => ContentType::InternalPost->value,
        'status' => ContentStatus::Published->value,
        'author_id' => $admin->id,
    ]);
    $externalPost = ContentItem::factory()->create([
        'type' => ContentType::ExternalPost->value,
        'status' => ContentStatus::Published->value,
        'author_id' => $admin->id,
    ]);
    $communityLink = ContentItem::factory()->create([
        'type' => ContentType::CommunityLink->value,
        'status' => ContentStatus::Published->value,
        'author_id' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->get('/admin/internal-posts')
        ->assertSuccessful()
        ->assertSee('Reads')
        ->assertSee((string) $internalPost->author->name);

    $this->actingAs($admin)
        ->get('/admin/external-posts')
        ->assertSuccessful()
        ->assertSee((string) $externalPost->author->name);

    $this->actingAs($admin)
        ->get('/admin/community-links')
        ->assertSuccessful()
        ->assertSee((string) $communityLink->author->name);
});

test('internal posts status tabs filter results', function () {
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

    $response = $this->actingAs($admin)->get('/admin/internal-posts?tab=published');

    $response->assertSuccessful();
    $response->assertSee((string) $published->author->name);
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

test('non admin users cannot access subscriber and editorial resources', function () {
    $author = User::factory()->author()->create();

    $this->actingAs($author)
        ->get('/admin/subscribers')
        ->assertForbidden();

    $this->actingAs($author)
        ->get('/admin/internal-posts')
        ->assertForbidden();

    $this->actingAs($author)
        ->get('/admin/external-posts')
        ->assertForbidden();

    $this->actingAs($author)
        ->get('/admin/community-links')
        ->assertForbidden();
});

test('author selection on internal post form includes non author users', function () {
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create([
        'name' => 'Regular Dashboard User',
    ]);
    $authorUser = User::factory()->author()->create([
        'name' => 'Editorial Author User',
    ]);

    $response = $this->actingAs($admin)->get('/admin/internal-posts/create');

    $response->assertSuccessful();
    $response->assertSee('Regular Dashboard User');
    $response->assertSee('Editorial Author User');
});
