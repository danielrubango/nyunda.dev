<?php

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\Comment;
use App\Models\ContentItem;
use App\Models\ContentTranslation;
use App\Models\User;
use Illuminate\Support\Carbon;

test('internal post can be rendered and markdown is sanitized', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'secure-internal-post',
        'title' => 'Secure internal post',
        'excerpt' => 'Internal excerpt',
        'body_markdown' => "# Heading\n\n<script>alert('xss')</script>\n\nParagraph",
    ]);

    $response = $this->get('/blog/fr/secure-internal-post');

    $response->assertSuccessful();
    $response->assertSee('Heading');
    $response->assertSee('Paragraph');
    $response->assertDontSee("<script>alert('xss')</script>", false);
    $response->assertDontSee("alert('xss')");
});

test('external post detail route redirects to external url', function () {
    $contentItem = ContentItem::factory()->published()->externalPost()->create();

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'external-post',
        'title' => 'External post',
        'excerpt' => 'External excerpt',
        'external_url' => 'https://example.com/article',
    ]);

    $response = $this->get('/blog/fr/external-post');

    $response->assertRedirect('https://example.com/article');
});

test('non published content cannot be displayed', function () {
    $pendingItem = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Pending->value,
        'type' => ContentType::InternalPost->value,
    ]);

    ContentTranslation::factory()->for($pendingItem)->forLocale('fr')->create([
        'slug' => 'pending-post',
        'title' => 'Pending post',
        'excerpt' => 'Pending excerpt',
    ]);

    $response = $this->get('/blog/fr/pending-post');

    $response->assertNotFound();
});

test('hide comment action is visible only to admin users', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'comment-visibility-post',
        'title' => 'Comment visibility post',
        'excerpt' => 'Comment excerpt',
    ]);

    $comment = Comment::factory()->create([
        'content_item_id' => $contentItem->id,
    ]);

    $adminResponse = $this->actingAs($admin)->get('/blog/fr/comment-visibility-post');
    $adminResponse->assertSuccessful();
    $adminResponse->assertSee(__('ui.blog.comments.hide'));
    $adminResponse->assertSee('x-data="commentActions(', false);
    $adminResponse->assertSee('x-on:submit.prevent="toggleVisibility($event)"', false);
    $adminResponse->assertSee('x-on:submit.prevent="deleteComment($event)"', false);

    $userResponse = $this->actingAs($user)->get('/blog/fr/comment-visibility-post');
    $userResponse->assertSuccessful();
    $userResponse->assertDontSee(route('comments.update', ['comment' => $comment]));
});

test('admin can see reads count on internal post page', function () {
    $admin = User::factory()->admin()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'reads_count' => 0,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'admin-reads-visible',
        'title' => 'Admin reads visible',
        'excerpt' => 'Admin reads excerpt',
    ]);

    $response = $this->actingAs($admin)->get('/blog/fr/admin-reads-visible');

    $response->assertSuccessful();
    $response->assertSee(__('ui.blog.reads', ['count' => (int) $contentItem->refresh()->reads_count]));
});

test('non admin user cannot see reads count on internal post page', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'reads_count' => 0,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'non-admin-reads-hidden',
        'title' => 'Non admin reads hidden',
        'excerpt' => 'Non admin reads excerpt',
    ]);

    $response = $this->actingAs($user)->get('/blog/fr/non-admin-reads-hidden');

    $response->assertSuccessful();
    $response->assertDontSee(__('ui.blog.reads', ['count' => (int) $contentItem->refresh()->reads_count]));
});

test('comment published dates are shown in human readable format', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-21 12:00:00'));

    try {
        $contentItem = ContentItem::factory()->published()->internalPost()->create();

        ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
            'slug' => 'human-comment-date-post',
            'title' => 'Human date post',
            'excerpt' => 'Human date excerpt',
        ]);

        Comment::factory()->create([
            'content_item_id' => $contentItem->id,
            'created_at' => now()->subSeconds(20),
        ]);

        Comment::factory()->create([
            'content_item_id' => $contentItem->id,
            'created_at' => now()->subMinutes(3),
        ]);

        $response = $this->get('/blog/fr/human-comment-date-post');

        $response->assertSuccessful();
        $response->assertSee("à l'instant");
        $response->assertSee('il y a 3 minutes');
    } finally {
        Carbon::setTestNow();
    }
});

test('admin can see hidden comments with hidden marker while non admin cannot', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'hidden-comment-admin-view',
        'title' => 'Hidden comment admin view',
        'excerpt' => 'Hidden comment excerpt',
    ]);

    Comment::factory()->create([
        'content_item_id' => $contentItem->id,
        'body_markdown' => 'Visible hidden comment body',
        'is_hidden' => true,
    ]);

    $adminResponse = $this->actingAs($admin)->get('/blog/fr/hidden-comment-admin-view');
    $adminResponse->assertSuccessful();
    $adminResponse->assertSee('Visible hidden comment body');
    $adminResponse->assertSee(':data-hidden-comment="hidden ? \'true\' : null"', false);
    $adminResponse->assertSee('x-show="hidden"', false);

    $userResponse = $this->actingAs($user)->get('/blog/fr/hidden-comment-admin-view');
    $userResponse->assertSuccessful();
    $userResponse->assertDontSee('Visible hidden comment body');
    $userResponse->assertDontSee(':data-hidden-comment="hidden ? \'true\' : null"', false);
});
