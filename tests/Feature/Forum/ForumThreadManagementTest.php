<?php

use App\Models\ForumThread;
use App\Models\User;

test('thread author can edit and update a thread', function () {
    $author = User::factory()->create();
    $thread = ForumThread::factory()->for($author, 'author')->create([
        'locale' => 'fr',
        'title' => 'Titre initial',
        'body_markdown' => 'Contenu initial suffisamment long pour validation.',
    ]);

    $editResponse = $this->actingAs($author)->get(route('forum.edit', $thread));
    $editResponse->assertSuccessful();
    $editResponse->assertSee('Modifier la discussion');
    $editResponse->assertSee('Titre initial');

    $updateResponse = $this->actingAs($author)->put(route('forum.update', $thread), [
        'locale' => 'en',
        'title' => 'Updated thread title',
        'body_markdown' => 'Updated body markdown content that is clearly long enough.',
    ]);

    $updateResponse->assertRedirect(route('forum.show', $thread));

    $this->assertDatabaseHas('forum_threads', [
        'id' => $thread->id,
        'locale' => 'en',
        'title' => 'Updated thread title',
    ]);
});

test('non owner cannot edit or update a thread', function () {
    $author = User::factory()->create();
    $thread = ForumThread::factory()->for($author, 'author')->create();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->get(route('forum.edit', $thread))
        ->assertForbidden();

    $this->actingAs($otherUser)
        ->put(route('forum.update', $thread), [
            'locale' => 'fr',
            'title' => 'Tentative non autorisée',
            'body_markdown' => 'Texte de tentative qui ne doit pas passer.',
        ])
        ->assertForbidden();
});

test('author can delete own thread and admin can delete any thread', function () {
    $author = User::factory()->create();
    $threadByAuthor = ForumThread::factory()->for($author, 'author')->create();

    $this->actingAs($author)
        ->delete(route('forum.destroy', $threadByAuthor))
        ->assertRedirect(route('forum.index'));

    $this->assertDatabaseMissing('forum_threads', [
        'id' => $threadByAuthor->id,
    ]);

    $otherAuthor = User::factory()->create();
    $threadByOtherAuthor = ForumThread::factory()->for($otherAuthor, 'author')->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('forum.destroy', $threadByOtherAuthor))
        ->assertRedirect(route('forum.index'));

    $this->assertDatabaseMissing('forum_threads', [
        'id' => $threadByOtherAuthor->id,
    ]);
});
