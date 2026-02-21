<?php

use App\Filament\Resources\ContentTranslations\ContentTranslationResource;
use App\Filament\Resources\ForumReplies\ForumReplyResource;
use App\Models\ContentItem;
use App\Models\ForumThread;
use App\Models\User;

test('admin can access content item edit page with relations', function () {
    $admin = User::factory()->admin()->create();
    $contentItem = ContentItem::factory()->create([
        'author_id' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->get("/admin/content-items/{$contentItem->id}/edit");

    $response->assertSuccessful();
});

test('admin can access forum thread edit page with replies relation', function () {
    $admin = User::factory()->admin()->create();
    $forumThread = ForumThread::factory()->create([
        'author_id' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->get("/admin/forum-threads/{$forumThread->slug}/edit");

    $response->assertSuccessful();
});

test('translation and forum reply resources are hidden from navigation', function () {
    expect(ContentTranslationResource::shouldRegisterNavigation())->toBeFalse();
    expect(ForumReplyResource::shouldRegisterNavigation())->toBeFalse();
});
