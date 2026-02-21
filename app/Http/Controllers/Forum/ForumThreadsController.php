<?php

namespace App\Http\Controllers\Forum;

use App\Actions\Content\RenderSafeMarkdown;
use App\Actions\Forum\CreateForumThread;
use App\Actions\Forum\DeleteForumThread;
use App\Actions\Forum\UpdateForumThread;
use App\Actions\Seo\BuildSeoMeta;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreForumThreadRequest;
use App\Http\Requests\Forum\UpdateForumThreadRequest;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ForumThreadsController extends Controller
{
    public function __construct(
        private readonly CreateForumThread $createForumThread,
        private readonly UpdateForumThread $updateForumThread,
        private readonly DeleteForumThread $deleteForumThread,
        private readonly RenderSafeMarkdown $renderSafeMarkdown,
        private readonly BuildSeoMeta $buildSeoMeta,
    ) {}

    public function index(): View
    {
        $this->applyInterfaceLocale();

        $threads = ForumThread::query()
            ->where('is_hidden', false)
            ->with('author')
            ->withCount(['replies' => fn ($query) => $query->where('is_hidden', false)])
            ->latest('created_at')
            ->paginate(20);

        return view('forum.index', [
            'threads' => $threads,
            'seo' => $this->buildSeoMeta->handle(
                title: __('ui.forum.title'),
                description: __('ui.forum.subtitle'),
                canonicalUrl: route('forum.index'),
            ),
        ]);
    }

    public function show(ForumThread $forumThread): View
    {
        $this->applyInterfaceLocale();

        if ($forumThread->is_hidden && ! $this->canViewHiddenThread($forumThread)) {
            abort(404);
        }

        $user = auth()->user();
        $isAdmin = $user instanceof User && $user->hasRole(UserRole::Admin);

        $forumThread->load([
            'author',
            'bestReply.user',
            'replies' => function ($query) use ($isAdmin): void {
                if (! $isAdmin) {
                    $query->where('is_hidden', false);
                }

                $query->with('user')->oldest('created_at');
            },
        ]);

        /** @var Collection<int, \App\Models\ForumReply> $replies */
        $replies = $forumThread->replies;

        return view('forum.show', [
            'forumThread' => $forumThread,
            'replies' => $replies,
            'renderedThreadBody' => $this->renderSafeMarkdown->handle($forumThread->body_markdown),
            'renderedReplies' => $replies->mapWithKeys(
                fn ($reply): array => [$reply->id => $this->renderSafeMarkdown->handle($reply->body_markdown)]
            ),
            'seo' => $this->buildSeoMeta->handle(
                title: $forumThread->title,
                description: mb_substr(trim($forumThread->body_markdown), 0, 160),
                canonicalUrl: route('forum.show', $forumThread),
            ),
        ]);
    }

    public function create(): View
    {
        $this->applyInterfaceLocale();

        $this->authorize('create', ForumThread::class);

        return view('forum.create', [
            'supportedLocales' => config('app.supported_locales', ['fr', 'en']),
            'defaultLocale' => auth()->user()?->preferred_locale ?? config('app.locale', 'fr'),
            'seo' => $this->buildSeoMeta->handle(
                title: __('ui.forum.create.title'),
                description: __('ui.forum.create.subtitle'),
                canonicalUrl: route('forum.create'),
            ),
        ]);
    }

    public function store(StoreForumThreadRequest $request): RedirectResponse
    {
        $this->authorize('create', ForumThread::class);

        /** @var User $author */
        $author = $request->user();

        $forumThread = $this->createForumThread->handle(
            author: $author,
            payload: $request->payload(),
        );

        return redirect()
            ->route('forum.show', $forumThread)
            ->with('status', __('ui.forum.status.created'));
    }

    public function edit(ForumThread $forumThread): View
    {
        $this->applyInterfaceLocale();

        $this->authorize('update', $forumThread);

        return view('forum.edit', [
            'forumThread' => $forumThread,
            'supportedLocales' => config('app.supported_locales', ['fr', 'en']),
            'seo' => $this->buildSeoMeta->handle(
                title: __('ui.forum.edit.title'),
                description: __('ui.forum.edit.subtitle'),
                canonicalUrl: route('forum.edit', $forumThread),
            ),
        ]);
    }

    public function update(UpdateForumThreadRequest $request, ForumThread $forumThread): RedirectResponse
    {
        $this->authorize('update', $forumThread);

        $this->updateForumThread->handle(
            forumThread: $forumThread,
            payload: $request->payload(),
        );

        return redirect()
            ->route('forum.show', $forumThread)
            ->with('status', __('ui.forum.status.updated'));
    }

    public function destroy(ForumThread $forumThread): RedirectResponse
    {
        $this->authorize('delete', $forumThread);

        $this->deleteForumThread->handle($forumThread);

        return redirect()
            ->route('forum.index')
            ->with('status', __('ui.forum.status.deleted'));
    }

    protected function canViewHiddenThread(ForumThread $forumThread): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if ($forumThread->author_id === $user->id) {
            return true;
        }

        return $user->hasRole(UserRole::Admin);
    }

    protected function applyInterfaceLocale(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        $locale = $user?->preferred_locale;

        if (is_string($locale) && in_array($locale, config('app.supported_locales', ['fr', 'en']), true)) {
            app()->setLocale($locale);

            return;
        }

        $preferredLocale = request()->getPreferredLanguage(config('app.supported_locales', ['fr', 'en']));

        if (is_string($preferredLocale) && $preferredLocale !== '') {
            app()->setLocale($preferredLocale);
        }
    }
}
