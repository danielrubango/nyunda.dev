<?php

use App\Enums\ContentStatus;
use App\Models\ContentItem;
use App\Models\ContentLike;
use App\Models\ContentTranslation;
use App\Models\User;

test('user can like and unlike a published internal post', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    $likeResponse = $this->actingAs($user)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]));

    $likeResponse->assertRedirect();
    expect(ContentLike::query()
        ->where('content_item_id', $contentItem->id)
        ->where('user_id', $user->id)
        ->count())->toBe(1);

    $unlikeResponse = $this->actingAs($user)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]));

    $unlikeResponse->assertRedirect();
    expect(ContentLike::query()
        ->where('content_item_id', $contentItem->id)
        ->where('user_id', $user->id)
        ->count())->toBe(0);
});

test('user cannot like non internal content', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->externalPost()->create();

    $response = $this->actingAs($user)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]));

    $response->assertForbidden();
});

test('user cannot like non published internal content', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Pending->value,
    ]);

    $response = $this->actingAs($user)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]));

    $response->assertForbidden();
});

test('guest is redirected to login when trying to like content', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();
    $translation = ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'guest-like-redirect',
        'title' => 'Guest like redirect',
        'excerpt' => 'Guest like redirect excerpt',
    ]);

    $returnTo = route('blog.show', [
        'locale' => $translation->locale,
        'slug' => $translation->slug,
    ]);

    $response = $this->from($returnTo)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('url.intended', $returnTo);
});

test('guest is returned to the article after login when like action required authentication', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();
    $translation = ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'guest-like-after-login',
        'title' => 'Guest like after login',
        'excerpt' => 'Guest like after login excerpt',
    ]);

    $returnTo = route('blog.show', [
        'locale' => $translation->locale,
        'slug' => $translation->slug,
    ]);

    $this->from($returnTo)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]))->assertRedirect(route('login'));

    $user = User::factory()->create();

    $loginResponse = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $loginResponse->assertRedirect($returnTo);
    $this->assertDatabaseMissing('content_likes', [
        'content_item_id' => $contentItem->id,
        'user_id' => $user->id,
    ]);
});

test('guest is returned to the article after registration when like action required authentication', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();
    $translation = ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'guest-like-after-register',
        'title' => 'Guest like after register',
        'excerpt' => 'Guest like after register excerpt',
    ]);

    $returnTo = route('blog.show', [
        'locale' => $translation->locale,
        'slug' => $translation->slug,
    ]);

    $this->from($returnTo)->post(route('content.likes.toggle', [
        'contentItem' => $contentItem,
    ]))->assertRedirect(route('login'));

    $registerResponse = $this->post(route('register.store'), [
        'name' => 'Guest Like User',
        'email' => 'guest-like-user@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $registerResponse->assertRedirect($returnTo);

    $registeredUser = User::query()->where('email', 'guest-like-user@example.com')->firstOrFail();
    $this->assertDatabaseMissing('content_likes', [
        'content_item_id' => $contentItem->id,
        'user_id' => $registeredUser->id,
    ]);
});
