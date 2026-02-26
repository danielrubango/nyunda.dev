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
    $adminResponse->assertSee('comment:request-delete', false);
    $adminResponse->assertSee('data-test="open-comment-delete-confirmation"', false);
    $adminResponse->assertSee('data-modal="confirm-comment-delete"', false);
    $adminResponse->assertSee(__('ui.blog.comments.confirm_delete_title'));
    $adminResponse->assertDontSee('group-hover:opacity-100');

    $userResponse = $this->actingAs($user)->get('/blog/fr/comment-visibility-post');
    $userResponse->assertSuccessful();
    $userResponse->assertDontSee(route('comments.update', ['comment' => $comment]));
    $userResponse->assertDontSee(__('ui.blog.comments.confirm_delete_title'));
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

test('comments section is rendered even when there are no comments', function () {
    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'show_comments' => true,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'comments-empty-visible',
        'title' => 'Comments empty visible',
        'excerpt' => 'Comments empty visible excerpt',
    ]);

    $response = $this->get('/blog/fr/comments-empty-visible');

    $response->assertSuccessful();
    $response->assertSee(__('ui.blog.comments.title'));
    $response->assertSee('class="ui-section-title"', false);
    $response->assertSee(__('ui.blog.comments.empty'));
    $response->assertSee(__('ui.blog.comments.login'));
});

test('authenticated comment form has no visible label and comments use compact sans-serif styling', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'show_comments' => true,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'comments-form-style',
        'title' => 'Comments form style',
        'excerpt' => 'Comments form style excerpt',
    ]);

    Comment::factory()->create([
        'content_item_id' => $contentItem->id,
    ]);

    $response = $this->actingAs($user)->get('/blog/fr/comments-form-style');

    $response->assertSuccessful();
    $response->assertSee('class="group space-y-1 p-5 sm:p-6', false);
    $response->assertSee('class="article-content max-w-none font-sans text-base"', false);
    $response->assertDontSee('<label for="body_markdown"', false);
    $response->assertSee('aria-label="'.__('ui.blog.comments.form_placeholder').'"', false);
});

test('reply button displays an icon for authenticated users', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'show_comments' => true,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'comments-reply-icon',
        'title' => 'Comments reply icon',
        'excerpt' => 'Comments reply icon excerpt',
    ]);

    Comment::factory()->create([
        'content_item_id' => $contentItem->id,
    ]);

    $response = $this->actingAs($user)->get('/blog/fr/comments-reply-icon');

    $response->assertSuccessful();
    $response->assertSeeInOrder([
        'title="'.__('ui.blog.comments.reply').'"',
        'd="M7 5V11C7 12.1046 7.89543 13 9 13H17"',
        __('ui.blog.comments.reply'),
    ], false);
});

test('child comments do not render the reply icon component in their header', function () {
    $user = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create([
        'show_comments' => true,
    ]);

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'comments-child-header-indicator',
        'title' => 'Comments child header indicator',
        'excerpt' => 'Comments child header indicator excerpt',
    ]);

    $parentComment = Comment::factory()->create([
        'content_item_id' => $contentItem->id,
    ]);

    Comment::factory()->create([
        'content_item_id' => $contentItem->id,
        'parent_id' => $parentComment->id,
    ]);

    $response = $this->actingAs($user)->get('/blog/fr/comments-child-header-indicator');

    $response->assertSuccessful();
    $response->assertDontSee('size-3 shrink-0 text-zinc-300', false);
    $response->assertSee('&hookrightarrow;', false);
});
