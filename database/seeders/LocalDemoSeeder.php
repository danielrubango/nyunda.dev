<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\SubscriberStatus;
use App\Models\Comment;
use App\Models\ContentItem;
use App\Models\ContentLike;
use App\Models\ContentTranslation;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\Project;
use App\Models\Subscriber;
use App\Models\Tag;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocalDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::query()->where('email', 'danielrubango@gmail.com')->exists()) {
            return;
        }

        $admin = User::factory()->admin()->withPublicProfile('drubango')->create([
            'name' => 'Daniel Rubango',
            'email' => 'danielrubango@gmail.com',
            'preferred_locale' => 'fr',
        ]);

        $communityUser = User::factory()->create([
            'name' => 'Junior Dev',
            'email' => 'user@nyunda.test',
            'preferred_locale' => 'fr',
        ]);

        $reader = User::factory()->create([
            'name' => 'Reader One',
            'email' => 'reader@nyunda.test',
            'preferred_locale' => 'en',
        ]);

        $tagLaravel = Tag::query()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'sort_order' => 1,
        ]);
        $tagPhp = Tag::query()->create([
            'name' => 'PHP',
            'slug' => 'php',
            'sort_order' => 2,
        ]);
        $tagAi = Tag::query()->create([
            'name' => 'IA',
            'slug' => 'ia',
            'sort_order' => 3,
        ]);
        $tagArchitecture = Tag::query()->create([
            'name' => 'Architecture',
            'slug' => 'architecture',
            'sort_order' => 4,
        ]);

        Project::query()->create([
            'name' => 'NYUNDA.DEV',
            'slug' => 'nyunda-dev',
            'description' => 'Blog technique orienté Laravel, PHP et IA.',
            'url' => 'https://nyunda.dev',
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        Tool::query()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'description' => 'Framework principal pour le back-end.',
            'url' => 'https://laravel.com',
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        Tool::query()->create([
            'name' => 'Filament',
            'slug' => 'filament',
            'description' => 'Panel admin moderne pour la gestion des contenus.',
            'url' => 'https://filamentphp.com',
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $publishedInternal = ContentItem::factory()
            ->internalPost()
            ->published()
            ->for($admin, 'author')
            ->create([
                'show_likes' => true,
                'show_comments' => true,
                'share_on_publish' => false,
            ]);

        ContentTranslation::query()->create([
            'content_item_id' => $publishedInternal->id,
            'locale' => 'fr',
            'title' => 'Structurer ses Actions Laravel pour un blog maintenable',
            'slug' => 'actions-laravel-blog-maintenable',
            'excerpt' => 'Retour terrain sur une architecture pragmatique pour les use cases.',
            'body_markdown' => "## Pourquoi des actions dédiées ?\n\nElles isolent la logique métier et gardent les contrôleurs légers.\n\n## Convention simple\n\n- 1 action = 1 cas d'usage\n- tests ciblés par action\n- dépendances explicites\n",
            'external_url' => null,
            'external_description' => null,
            'external_site_name' => null,
            'external_og_image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ContentTranslation::query()->create([
            'content_item_id' => $publishedInternal->id,
            'locale' => 'en',
            'title' => 'Organizing Laravel Actions for a Maintainable Blog',
            'slug' => 'organizing-laravel-actions-maintainable-blog',
            'excerpt' => 'Pragmatic patterns to keep controllers clean and business logic testable.',
            'body_markdown' => "## Why dedicated actions?\n\nThey isolate business rules and keep controllers lightweight.\n\n## Simple convention\n\n- 1 action = 1 use case\n- focused tests per action\n- explicit dependencies\n",
            'external_url' => null,
            'external_description' => null,
            'external_site_name' => null,
            'external_og_image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $publishedInternal->tags()->sync([
            $tagLaravel->id,
            $tagPhp->id,
            $tagArchitecture->id,
        ]);

        $internalWithoutComments = ContentItem::factory()
            ->internalPost()
            ->published()
            ->for($admin, 'author')
            ->create([
                'show_likes' => true,
                'show_comments' => false,
                'share_on_publish' => false,
            ]);

        ContentTranslation::query()->create([
            'content_item_id' => $internalWithoutComments->id,
            'locale' => 'fr',
            'title' => 'Checklist de mise en production Laravel sur VPS',
            'slug' => 'checklist-mise-en-production-laravel-vps',
            'excerpt' => 'Les points minimum pour livrer sans mauvaise surprise.',
            'body_markdown' => "Checklist courte : queue worker, scheduler, cache config, monitoring.\n",
            'external_url' => null,
            'external_description' => null,
            'external_site_name' => null,
            'external_og_image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $internalWithoutComments->tags()->sync([
            $tagLaravel->id,
            $tagArchitecture->id,
        ]);

        $draftInternal = ContentItem::factory()
            ->internalPost()
            ->for($admin, 'author')
            ->create([
                'status' => ContentStatus::Draft->value,
                'show_likes' => true,
                'show_comments' => true,
                'share_on_publish' => false,
            ]);

        ContentTranslation::query()->create([
            'content_item_id' => $draftInternal->id,
            'locale' => 'fr',
            'title' => 'Brouillon: observabilité des jobs Laravel',
            'slug' => 'brouillon-observabilite-jobs-laravel',
            'excerpt' => 'Article en cours de rédaction.',
            'body_markdown' => "Draft en préparation.\n",
            'external_url' => null,
            'external_description' => null,
            'external_site_name' => null,
            'external_og_image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $publishedExternal = ContentItem::factory()
            ->externalPost()
            ->published()
            ->for($admin, 'author')
            ->create([
                'show_likes' => false,
                'show_comments' => false,
                'share_on_publish' => false,
            ]);

        ContentTranslation::query()->create([
            'content_item_id' => $publishedExternal->id,
            'locale' => 'fr',
            'title' => 'Ressource externe: scaling Laravel jobs',
            'slug' => 'ressource-externe-scaling-laravel-jobs',
            'excerpt' => 'Un bon retour d’expérience sur le scaling des workers.',
            'body_markdown' => null,
            'external_url' => 'https://laravel.com/docs/queues',
            'external_description' => 'Guide officiel des queues Laravel.',
            'external_site_name' => 'Laravel Documentation',
            'external_og_image_url' => 'https://laravel.com/img/logomark.min.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ContentTranslation::query()->create([
            'content_item_id' => $publishedExternal->id,
            'locale' => 'en',
            'title' => 'External resource: scaling Laravel jobs',
            'slug' => 'external-resource-scaling-laravel-jobs',
            'excerpt' => 'A solid field report on scaling workers.',
            'body_markdown' => null,
            'external_url' => 'https://laravel.com/docs/queues',
            'external_description' => 'Official Laravel queue documentation.',
            'external_site_name' => 'Laravel Documentation',
            'external_og_image_url' => 'https://laravel.com/img/logomark.min.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $publishedExternal->tags()->sync([
            $tagLaravel->id,
            $tagAi->id,
        ]);

        $pendingCommunityLink = ContentItem::factory()
            ->communityLink()
            ->for($communityUser, 'author')
            ->create([
                'status' => ContentStatus::Pending->value,
                'show_likes' => false,
                'show_comments' => false,
                'share_on_publish' => false,
            ]);

        ContentTranslation::query()->create([
            'content_item_id' => $pendingCommunityLink->id,
            'locale' => 'fr',
            'title' => 'Proposition communauté: architecture hexagonale',
            'slug' => 'proposition-communaute-architecture-hexagonale',
            'excerpt' => 'Soumission en attente de validation admin.',
            'body_markdown' => null,
            'external_url' => 'https://martinfowler.com',
            'external_description' => 'Référence sur les patterns d’architecture.',
            'external_site_name' => 'Martin Fowler',
            'external_og_image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $publishedCommunityLink = ContentItem::factory()
            ->communityLink()
            ->published()
            ->for($communityUser, 'author')
            ->create([
                'show_likes' => false,
                'show_comments' => false,
                'share_on_publish' => false,
            ]);

        ContentTranslation::query()->create([
            'content_item_id' => $publishedCommunityLink->id,
            'locale' => 'fr',
            'title' => 'Lien communauté: bonnes pratiques API REST',
            'slug' => 'lien-communaute-bonnes-pratiques-api-rest',
            'excerpt' => 'Lien validé et publié.',
            'body_markdown' => null,
            'external_url' => 'https://jsonapi.org',
            'external_description' => 'Spécification JSON:API pour des API cohérentes.',
            'external_site_name' => 'JSON:API',
            'external_og_image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Comment::query()->create([
            'content_item_id' => $publishedInternal->id,
            'user_id' => $communityUser->id,
            'body_markdown' => "Très utile. J'aimerais voir un exemple avec validation multilingue.",
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Comment::query()->create([
            'content_item_id' => $publishedInternal->id,
            'user_id' => $reader->id,
            'body_markdown' => 'Merci pour ce retour terrain.',
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Comment::query()->create([
            'content_item_id' => $publishedInternal->id,
            'user_id' => $reader->id,
            'body_markdown' => 'Commentaire masqué de démonstration.',
            'is_hidden' => true,
            'hidden_at' => now(),
            'hidden_by_id' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ContentLike::query()->insert([
            [
                'content_item_id' => $publishedInternal->id,
                'user_id' => $communityUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'content_item_id' => $publishedInternal->id,
                'user_id' => $reader->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'content_item_id' => $internalWithoutComments->id,
                'user_id' => $communityUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $forumThreadFr = ForumThread::query()->create([
            'author_id' => $communityUser->id,
            'locale' => 'fr',
            'title' => 'Comment structurer un module forum avec actions ?',
            'slug' => 'comment-structurer-module-forum-actions',
            'body_markdown' => "J'aimerais un pattern simple pour gérer création, édition, modération et best reply.",
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by_id' => null,
            'best_reply_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $bestReply = ForumReply::query()->create([
            'forum_thread_id' => $forumThreadFr->id,
            'user_id' => $admin->id,
            'body_markdown' => "Commence par une action par cas d'usage, puis un contrôleur CRUD strict.",
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ForumReply::query()->create([
            'forum_thread_id' => $forumThreadFr->id,
            'user_id' => $reader->id,
            'body_markdown' => 'Ajoute une policy claire et des tests de workflow.',
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $forumThreadFr->update([
            'best_reply_id' => $bestReply->id,
        ]);

        ForumThread::query()->create([
            'author_id' => $reader->id,
            'locale' => 'en',
            'title' => 'Queue retries for social sharing',
            'slug' => 'queue-retries-for-social-sharing',
            'body_markdown' => 'How do you tune retry backoff for external APIs?',
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by_id' => null,
            'best_reply_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ForumThread::query()->create([
            'author_id' => $communityUser->id,
            'locale' => 'fr',
            'title' => 'Thread caché de modération',
            'slug' => 'thread-cache-moderation',
            'body_markdown' => 'Thread utilisé pour vérifier les droits de modération.',
            'is_hidden' => true,
            'hidden_at' => now(),
            'hidden_by_id' => $admin->id,
            'best_reply_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Subscriber::query()->create([
            'email' => 'newsletter.pending@nyunda.test',
            'status' => SubscriberStatus::Pending->value,
            'confirmation_token' => Str::random(40),
            'confirmed_at' => null,
            'locale' => 'fr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Subscriber::query()->create([
            'email' => 'newsletter.confirmed@nyunda.test',
            'status' => SubscriberStatus::Confirmed->value,
            'confirmation_token' => null,
            'confirmed_at' => now(),
            'locale' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Subscriber::query()->create([
            'email' => 'newsletter.unsubscribed@nyunda.test',
            'status' => SubscriberStatus::Unsubscribed->value,
            'confirmation_token' => null,
            'confirmed_at' => now()->subDays(10),
            'locale' => 'fr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
