<x-layouts.public :seo="$seo">
    <div class="ui-container">
        <div class="mx-auto max-w-3xl space-y-8">
            <a href="{{ route('forum.index') }}" class="text-sm font-medium text-zinc-600 no-underline hover:text-brand-700">
                {{ __('ui.forum.back_forum') }}
            </a>

            @if (session('status'))
                <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
            @endif

            <header class="space-y-4 border-b border-zinc-200 pb-6">
                <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-wide text-zinc-500">
                    <span>{{ strtoupper($forumThread->locale) }}</span>
                    <span>•</span>
                    <span>{{ $forumThread->author->name }}</span>
                    <span>•</span>
                    <span>{{ $forumThread->created_at?->format('Y-m-d H:i') }}</span>
                </div>
                <h1 class="font-title text-4xl font-semibold tracking-tight text-zinc-900 sm:text-5xl">{{ $forumThread->title }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    @can('moderate', $forumThread)
                        <form method="POST" action="{{ route('forum.visibility.update', $forumThread) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_hidden" value="{{ $forumThread->is_hidden ? '0' : '1' }}">
                            <button type="submit" class="border-b border-transparent pb-0.5 text-zinc-600 hover:border-zinc-400 hover:text-zinc-900">
                                {{ $forumThread->is_hidden ? __('ui.forum.show_thread') : __('ui.forum.hide_thread') }}
                            </button>
                        </form>
                    @endcan
                    @can('update', $forumThread)
                        <a href="{{ route('forum.edit', $forumThread) }}" class="border-b border-transparent pb-0.5 text-zinc-600 no-underline hover:border-zinc-400 hover:text-zinc-900">
                            {{ __('ui.forum.edit_action') }}
                        </a>
                    @endcan
                    @can('delete', $forumThread)
                        <form method="POST" action="{{ route('forum.destroy', $forumThread) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="border-b border-transparent pb-0.5 text-zinc-600 hover:border-zinc-400 hover:text-zinc-900">
                                {{ __('ui.forum.delete_action') }}
                            </button>
                        </form>
                    @endcan
                </div>
                @if ($forumThread->is_hidden)
                    <x-ui.badge>{{ __('ui.forum.thread_hidden') }}</x-ui.badge>
                @endif
            </header>

            <article class="article-content">
                {!! $renderedThreadBody !!}
            </article>

            <section id="replies" class="space-y-5">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">{{ __('ui.forum.replies_title') }}</h2>

                @auth
                    <x-ui.card>
                        <form method="POST" action="{{ route('forum.replies.store', ['forumThread' => $forumThread]) }}" class="space-y-3">
                            @csrf
                            <label for="body_markdown" class="block text-sm font-medium text-zinc-700">{{ __('ui.forum.your_reply') }}</label>
                            <textarea id="body_markdown" name="body_markdown" rows="5" class="w-full border border-zinc-300 px-3 py-2 text-sm">{{ old('body_markdown') }}</textarea>
                            @error('body_markdown')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="h-10 border border-brand-700 px-4 text-sm font-medium text-brand-700 transition-colors hover:bg-brand-50">
                                {{ __('ui.forum.publish_reply') }}
                            </button>
                        </form>
                    </x-ui.card>
                @else
                    <x-ui.alert>{{ __('ui.forum.login_reply') }}</x-ui.alert>
                @endauth

                <div class="space-y-4">
                    @forelse ($replies as $reply)
                        <x-ui.card as="article" class="{{ $forumThread->best_reply_id === $reply->id ? 'border-green-300 bg-green-50/40' : '' }}">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-500">
                                    <span>{{ $reply->user->name }}</span>
                                    <span>•</span>
                                    <span>{{ $reply->created_at?->format('Y-m-d H:i') }}</span>
                                    @if ($forumThread->best_reply_id === $reply->id)
                                        <x-ui.badge variant="success">{{ __('ui.forum.best_reply') }}</x-ui.badge>
                                    @endif
                                    @if ($reply->is_hidden)
                                        <x-ui.badge>{{ __('ui.forum.masked') }}</x-ui.badge>
                                    @endif
                                </div>

                                <div class="article-content max-w-none text-base">
                                    {!! $renderedReplies[$reply->id] !!}
                                </div>

                                <div class="flex flex-wrap items-center gap-4 text-sm">
                                    @can('markBestReply', $forumThread)
                                        @if ($forumThread->best_reply_id !== $reply->id)
                                            <form method="POST" action="{{ route('forum.replies.mark-best', ['forumThread' => $forumThread, 'forumReply' => $reply]) }}">
                                                @csrf
                                                <button type="submit" class="border-b border-transparent pb-0.5 text-zinc-600 hover:border-zinc-400 hover:text-zinc-900">
                                                    {{ __('ui.forum.mark_best_reply') }}
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    @can('moderate', $reply)
                                        <form method="POST" action="{{ route('forum.replies.update', ['forumReply' => $reply]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_hidden" value="{{ $reply->is_hidden ? '0' : '1' }}">
                                            <button type="submit" class="border-b border-transparent pb-0.5 text-zinc-600 hover:border-zinc-400 hover:text-zinc-900">
                                                {{ $reply->is_hidden ? __('ui.forum.show') : __('ui.forum.hide') }}
                                            </button>
                                        </form>
                                    @endcan

                                    @can('delete', $reply)
                                        <form method="POST" action="{{ route('forum.replies.destroy', ['forumReply' => $reply]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="border-b border-transparent pb-0.5 text-zinc-600 hover:border-zinc-400 hover:text-zinc-900">
                                                {{ __('ui.forum.delete_action') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </x-ui.card>
                    @empty
                        <x-ui.alert>{{ __('ui.forum.no_replies') }}</x-ui.alert>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts.public>
