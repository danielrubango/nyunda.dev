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
    $adminResponse->assertSee('x-on:submit.prevent="deleteComment($event,', false);
    $adminResponse->assertSee('data-test="open-comment-delete-confirmation"', false);
    $adminResponse->assertSee('data-modal="confirm-comment-deletion-'.$comment->id.'"', false);
    $adminResponse->assertSee(__('ui.blog.comments.confirm_delete_title'));
    $adminResponse->assertDontSee('group-hover:opacity-100');

    $userResponse = $this->actingAs($user)->get('/blog/fr/comment-visibility-post');
    $userResponse->assertSuccessful();
    $userResponse->assertDontSee(route('comments.update', ['comment' => $comment]));
    $userResponse->assertDontSee(route('comments.destroy', ['comment' => $comment]));
});

test('comment author can open delete modal even when first comment belongs to another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $contentItem = ContentItem::factory()->published()->internalPost()->create();

    ContentTranslation::factory()->for($contentItem)->forLocale('fr')->create([
        'slug' => 'comment-delete-modal-author-case',
        'title' => 'Comment delete modal author case',
        'excerpt' => 'Comment delete modal author case excerpt',
    ]);

    Comment::factory()->create([
        'content_item_id' => $contentItem->id,
        'user_id' => $otherUser->id,
    ]);

    $ownComment = Comment::factory()->create([
        'content_item_id' => $contentItem->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get('/blog/fr/comment-delete-modal-author-case');

    $response->assertSuccessful();
    $response->assertSee(__('ui.blog.comments.confirm_delete_title'));
    $response->assertSee('data-test="open-comment-delete-confirmation"', false);
    $response->assertSee(route('comments.destroy', ['comment' => $ownComment]), false);
    $response->assertSee('x-on:submit.prevent="deleteComment($event,', false);
    $response->assertSee('data-modal="confirm-comment-deletion-'.$ownComment->id.'"', false);
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
    $response->assertSee('class="group scroll-mt-24 space-y-1 p-5 sm:p-6', false);
    $response->assertSee('class="article-content max-w-none font-sans text-base"', false);
    $response->assertDontSee('<label for="body_markdown"', false);
    $response->assertSee('aria-label="'.__('ui.blog.comments.form_placeholder').'"', false);
});

test('blog show uses manual previous and next article links when provided', function () {
    $previousItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subDays(2),
    ]);
    $currentItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subDay(),
    ]);
    $nextItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now(),
    ]);

    $previousTranslation = ContentTranslation::factory()->for($previousItem)->forLocale('fr')->create([
        'slug' => 'adjacent-manual-prev',
        'title' => 'Article manuel precedent',
        'excerpt' => 'Prev excerpt',
    ]);
    $currentTranslation = ContentTranslation::factory()->for($currentItem)->forLocale('fr')->create([
        'slug' => 'adjacent-manual-current',
        'title' => 'Article manuel courant',
        'excerpt' => 'Current excerpt',
    ]);
    $nextTranslation = ContentTranslation::factory()->for($nextItem)->forLocale('fr')->create([
        'slug' => 'adjacent-manual-next',
        'title' => 'Article manuel suivant',
        'excerpt' => 'Next excerpt',
    ]);

    $currentItem->forceFill([
        'prev_article_id' => $previousItem->id,
        'next_article_id' => $nextItem->id,
    ])->save();

    $response = $this->get('/blog/fr/'.$currentTranslation->slug);

    $response->assertSuccessful();
    $response->assertSee(__('ui.blog.navigation.previous'));
    $response->assertSee(__('ui.blog.navigation.next'));
    $response->assertSee($previousTranslation->title);
    $response->assertSee($nextTranslation->title);
    $response->assertSee(route('blog.show', ['locale' => 'fr', 'slug' => $previousTranslation->slug]), false);
    $response->assertSee(route('blog.show', ['locale' => 'fr', 'slug' => $nextTranslation->slug]), false);
    $response->assertSeeInOrder([
        __('ui.blog.share.menu'),
        __('ui.blog.navigation.previous'),
        __('ui.blog.comments.title'),
    ]);
});

test('blog show falls back to closest published internal articles when manual links are not set', function () {
    $previousItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subHours(6),
    ]);
    $currentItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subHours(4),
    ]);
    $nextItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subHours(2),
    ]);

    $previousTranslation = ContentTranslation::factory()->for($previousItem)->forLocale('fr')->create([
        'slug' => 'adjacent-auto-prev',
        'title' => 'Article auto precedent',
        'excerpt' => 'Prev excerpt',
    ]);
    $currentTranslation = ContentTranslation::factory()->for($currentItem)->forLocale('fr')->create([
        'slug' => 'adjacent-auto-current',
        'title' => 'Article auto courant',
        'excerpt' => 'Current excerpt',
    ]);
    $nextTranslation = ContentTranslation::factory()->for($nextItem)->forLocale('fr')->create([
        'slug' => 'adjacent-auto-next',
        'title' => 'Article auto suivant',
        'excerpt' => 'Next excerpt',
    ]);

    $response = $this->get('/blog/fr/'.$currentTranslation->slug);

    $response->assertSuccessful();
    $response->assertSee($previousTranslation->title);
    $response->assertSee($nextTranslation->title);
    $response->assertSee(route('blog.show', ['locale' => 'fr', 'slug' => $previousTranslation->slug]), false);
    $response->assertSee(route('blog.show', ['locale' => 'fr', 'slug' => $nextTranslation->slug]), false);
});

test('blog show ignores invalid manual links and falls back to automatic adjacent articles', function () {
    $previousItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subHours(5),
    ]);
    $currentItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subHours(3),
    ]);
    $nextItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subHour(),
    ]);
    $invalidPrevious = ContentItem::factory()->internalPost()->create([
        'status' => ContentStatus::Pending->value,
        'published_at' => now()->addHour(),
    ]);
    $invalidNext = ContentItem::factory()->published()->externalPost()->create([
        'published_at' => now()->subMinutes(30),
    ]);

    $previousTranslation = ContentTranslation::factory()->for($previousItem)->forLocale('fr')->create([
        'slug' => 'adjacent-fallback-prev',
        'title' => 'Article fallback precedent',
        'excerpt' => 'Prev excerpt',
    ]);
    $currentTranslation = ContentTranslation::factory()->for($currentItem)->forLocale('fr')->create([
        'slug' => 'adjacent-fallback-current',
        'title' => 'Article fallback courant',
        'excerpt' => 'Current excerpt',
    ]);
    $nextTranslation = ContentTranslation::factory()->for($nextItem)->forLocale('fr')->create([
        'slug' => 'adjacent-fallback-next',
        'title' => 'Article fallback suivant',
        'excerpt' => 'Next excerpt',
    ]);

    ContentTranslation::factory()->for($invalidPrevious)->forLocale('fr')->create([
        'slug' => 'adjacent-invalid-prev',
        'title' => 'Article invalide precedent',
        'excerpt' => 'Invalid prev',
    ]);
    ContentTranslation::factory()->for($invalidNext)->forLocale('fr')->create([
        'slug' => 'adjacent-invalid-next',
        'title' => 'Article invalide suivant',
        'excerpt' => 'Invalid next',
        'external_url' => 'https://example.com/invalid-next',
    ]);

    $currentItem->forceFill([
        'prev_article_id' => $invalidPrevious->id,
        'next_article_id' => $invalidNext->id,
    ])->save();

    $response = $this->get('/blog/fr/'.$currentTranslation->slug);

    $response->assertSuccessful();
    $response->assertSee($previousTranslation->title);
    $response->assertSee($nextTranslation->title);
    $response->assertDontSee('Article invalide precedent');
    $response->assertDontSee('Article invalide suivant');
});

test('blog show renders only next link when no previous candidate exists', function () {
    $currentItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subDays(2),
    ]);
    $nextItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subDay(),
    ]);

    $currentTranslation = ContentTranslation::factory()->for($currentItem)->forLocale('fr')->create([
        'slug' => 'adjacent-one-side-current',
        'title' => 'Article un cote courant',
        'excerpt' => 'Current excerpt',
    ]);
    $nextTranslation = ContentTranslation::factory()->for($nextItem)->forLocale('fr')->create([
        'slug' => 'adjacent-one-side-next',
        'title' => 'Article un cote suivant',
        'excerpt' => 'Next excerpt',
    ]);

    $response = $this->get('/blog/fr/'.$currentTranslation->slug);

    $response->assertSuccessful();
    $response->assertDontSee(__('ui.blog.navigation.previous'));
    $response->assertSee(__('ui.blog.navigation.next'));
    $response->assertSee($nextTranslation->title);
});

test('blog show never picks external post as adjacent link', function () {
    $currentItem = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subHours(3),
    ]);

    $closestExternal = ContentItem::factory()->published()->externalPost()->create([
        'published_at' => now()->subHours(2),
    ]);

    $nextInternal = ContentItem::factory()->published()->internalPost()->create([
        'published_at' => now()->subHour(),
    ]);

    $currentTranslation = ContentTranslation::factory()->for($currentItem)->forLocale('fr')->create([
        'slug' => 'adjacent-internal-only-current',
        'title' => 'Article interne courant',
        'excerpt' => 'Current excerpt',
    ]);

    ContentTranslation::factory()->for($closestExternal)->forLocale('fr')->create([
        'slug' => 'adjacent-internal-only-external',
        'title' => 'Article externe proche',
        'excerpt' => 'External excerpt',
        'external_url' => 'https://example.com/external-nearest',
    ]);

    $nextInternalTranslation = ContentTranslation::factory()->for($nextInternal)->forLocale('fr')->create([
        'slug' => 'adjacent-internal-only-next',
        'title' => 'Article interne suivant',
        'excerpt' => 'Next excerpt',
    ]);

    $response = $this->get('/blog/fr/'.$currentTranslation->slug);

    $response->assertSuccessful();
    $response->assertSee('Article interne suivant');
    $response->assertDontSee('Article externe proche');
    $response->assertSee(route('blog.show', [
        'locale' => 'fr',
        'slug' => $nextInternalTranslation->slug,
    ]), false);
});
