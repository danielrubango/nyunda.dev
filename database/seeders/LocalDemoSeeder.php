<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Enums\SubscriberStatus;
use App\Enums\UserRole;
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
        $adminEmail = (string) config('app.admin_email');

        if (ContentItem::query()->count() >= 10) {
            return;
        }

        $admin = User::query()->firstOrCreate([
            'email' => $adminEmail,
        ], [
            'name' => 'Daniel Rubango',
            'password' => 'password',
            'role' => UserRole::Admin->value,
            'preferred_locale' => 'fr',
            'is_profile_public' => true,
            'public_profile_slug' => 'drubango',
        ]);
        $admin->forceFill([
            'name' => 'Daniel Rubango',
            'role' => UserRole::Admin->value,
            'preferred_locale' => 'fr',
            'is_profile_public' => true,
            'public_profile_slug' => 'drubango',
        ])->save();

        $communityUser = User::query()->firstOrCreate([
            'email' => 'user@nyunda.test',
        ], [
            'name' => 'Junior Dev',
            'password' => 'password',
            'preferred_locale' => 'fr',
        ]);
        $communityUser->forceFill([
            'name' => 'Junior Dev',
            'preferred_locale' => 'fr',
        ])->save();

        $reader = User::query()->firstOrCreate([
            'email' => 'reader@nyunda.test',
        ], [
            'name' => 'Reader One',
            'password' => 'password',
            'preferred_locale' => 'en',
        ]);
        $reader->forceFill([
            'name' => 'Reader One',
            'preferred_locale' => 'en',
        ])->save();

        $tagLaravel = Tag::query()->firstOrCreate([
            'slug' => 'laravel',
        ], [
            'name' => 'Laravel',
            'sort_order' => 1,
        ]);
        $tagPhp = Tag::query()->firstOrCreate([
            'slug' => 'php',
        ], [
            'name' => 'PHP',
            'sort_order' => 2,
        ]);
        $tagAi = Tag::query()->firstOrCreate([
            'slug' => 'ia',
        ], [
            'name' => 'IA',
            'sort_order' => 3,
        ]);
        $tagArchitecture = Tag::query()->firstOrCreate([
            'slug' => 'architecture',
        ], [
            'name' => 'Architecture',
            'sort_order' => 4,
        ]);

        Project::query()->firstOrCreate([
            'slug' => 'nyunda-dev',
        ], [
            'name' => 'NYUNDA.DEV',
            'description' => 'Blog technique orienté Laravel, PHP et IA.',
            'url' => 'https://nyunda.dev',
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        Tool::query()->firstOrCreate([
            'slug' => 'laravel',
        ], [
            'name' => 'Laravel',
            'description' => 'Framework principal pour le back-end.',
            'url' => 'https://laravel.com',
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        Tool::query()->firstOrCreate([
            'slug' => 'filament',
        ], [
            'name' => 'Filament',
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

        $pendingExternalLink = ContentItem::factory()
            ->externalPost()
            ->for($communityUser, 'author')
            ->create([
                'status' => ContentStatus::Pending->value,
                'show_likes' => false,
                'show_comments' => false,
                'share_on_publish' => false,
            ]);

        ContentTranslation::query()->create([
            'content_item_id' => $pendingExternalLink->id,
            'locale' => 'fr',
            'title' => 'Proposition externe: architecture hexagonale',
            'slug' => 'proposition-externe-architecture-hexagonale',
            'excerpt' => 'Soumission externe en attente de validation admin.',
            'body_markdown' => null,
            'external_url' => 'https://martinfowler.com',
            'external_description' => 'Référence sur les patterns d’architecture.',
            'external_site_name' => 'Martin Fowler',
            'external_og_image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $publishedExternalLink = ContentItem::factory()
            ->externalPost()
            ->published()
            ->for($communityUser, 'author')
            ->create([
                'show_likes' => false,
                'show_comments' => false,
                'share_on_publish' => false,
            ]);

        ContentTranslation::query()->create([
            'content_item_id' => $publishedExternalLink->id,
            'locale' => 'fr',
            'title' => 'Ressource externe: bonnes pratiques API REST',
            'slug' => 'ressource-externe-bonnes-pratiques-api-rest',
            'excerpt' => 'Ressource externe validée et publiée.',
            'body_markdown' => null,
            'external_url' => 'https://jsonapi.org',
            'external_description' => 'Spécification JSON:API pour des API cohérentes.',
            'external_site_name' => 'JSON:API',
            'external_og_image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (range(1, 8) as $index) {
            $isExternal = $index % 2 === 0;
            $contentItem = ContentItem::factory()
                ->for($admin, 'author')
                ->published()
                ->state([
                    'type' => $isExternal ? ContentType::ExternalPost->value : ContentType::InternalPost->value,
                    'show_likes' => ! $isExternal,
                    'show_comments' => ! $isExternal,
                ])
                ->create();

            ContentTranslation::query()->create([
                'content_item_id' => $contentItem->id,
                'locale' => 'fr',
                'title' => $isExternal
                    ? 'Ressource externe #'.$index.' pour progresser en Laravel'
                    : 'Article interne #'.$index.' sur Laravel en production',
                'slug' => $isExternal
                    ? 'ressource-externe-'.$index.'-laravel'
                    : 'article-interne-'.$index.'-laravel-production',
                'excerpt' => $isExternal
                    ? 'Sélection de ressource externe utile pour aller plus vite.'
                    : 'Retour concret et synthèse actionnable pour les projets Laravel.',
                'body_markdown' => $isExternal ? null : "## Note #{$index}\n\nContenu interne de démonstration.\n",
                'external_url' => $isExternal ? 'https://example.com/resource-'.$index : null,
                'external_description' => $isExternal ? 'Description externe de démonstration #'.$index : null,
                'external_site_name' => $isExternal ? 'Example' : null,
                'external_og_image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (! $isExternal) {
                $contentItem->tags()->sync([$tagLaravel->id, $tagPhp->id]);
            } else {
                $contentItem->tags()->sync([$tagLaravel->id, $tagAi->id]);
            }
        }

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
