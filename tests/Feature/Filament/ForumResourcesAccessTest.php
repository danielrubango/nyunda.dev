<?php

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;

test('admin can access forum thread resource list', function () {
    $admin = User::factory()->admin()->create();
    $thread = ForumThread::factory()->create([
        'title' => 'Thread admin list',
    ]);

    $response = $this->actingAs($admin)->get('/admin/forum-threads');

    $response->assertSuccessful();
    $response->assertSee('Thread admin list');
    $response->assertSee((string) $thread->id);
});

test('admin can access forum reply resource list', function () {
    $admin = User::factory()->admin()->create();
    $thread = ForumThread::factory()->create([
        'title' => 'Thread for replies admin list',
    ]);
    $reply = ForumReply::factory()->for($thread)->create();

    $response = $this->actingAs($admin)->get('/admin/forum-replies');

    $response->assertSuccessful();
    $response->assertSee('Thread for replies admin list');
    $response->assertSee((string) $reply->id);
});

test('non admin users cannot access forum resources in admin panel', function () {
    $author = User::factory()->author()->create();

    $this->actingAs($author)
        ->get('/admin/forum-threads')
        ->assertForbidden();

    $this->actingAs($author)
        ->get('/admin/forum-replies')
        ->assertForbidden();
});
