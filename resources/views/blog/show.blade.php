<x-layouts.public :seo="$seo">
    <div class="ui-container">
        <div class="mx-auto max-w-3xl space-y-8">
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-zinc-600 no-underline hover:text-brand-700">
                {{ __('ui.blog.back') }}
            </a>

            <header class="space-y-4 border-b border-zinc-200 pb-6">
                @if ($translation->featured_image_url)
                    <img
                        src="{{ $translation->featured_image_url }}"
                        alt="{{ $translation->title }}"
                        width="1200"
                        height="675"
                        loading="eager"
                        class="aspect-video w-full border border-zinc-200 object-cover"
                    >
                @endif

                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge variant="internal">{{ __('ui.blog.content_types.internal_post') }}</x-ui.badge>
                    <span class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ strtoupper($translation->locale) }}</span>
                </div>

                <h1 class="font-title text-4xl font-semibold tracking-tight text-zinc-900 sm:text-5xl">{{ $translation->title }}</h1>
                <p class="max-w-[75ch] text-lg text-zinc-600">{{ $translation->excerpt }}</p>
                <p class="text-sm text-zinc-500">
                    {{ __('ui.blog.published_by', [
                        'date' => $contentItem->published_at?->format('Y-m-d') ?? $contentItem->created_at?->format('Y-m-d'),
                        'author' => $contentItem->author->name,
                    ]) }}
                </p>
            </header>

            <article class="article-content">
                {!! $renderedBody !!}
            </article>

            <section class="flex flex-wrap items-center gap-4 border-y border-zinc-200 py-5">
                @if ($contentItem->show_likes)
                    <div class="flex items-center gap-2 text-sm text-zinc-700">
                        <span class="font-medium">{{ __('ui.blog.likes', ['count' => (int) $contentItem->likes_count]) }}</span>
                        @auth
                            <form method="POST" action="{{ route('content.likes.toggle', ['contentItem' => $contentItem]) }}">
                                @csrf
                                <button type="submit" class="border-b border-transparent pb-0.5 text-sm text-zinc-600 hover:border-zinc-400 hover:text-zinc-900">
                                    {{ __('ui.blog.like_toggle') }}
                                </button>
                            </form>
                        @endauth
                    </div>
                @endif

                <div class="ms-auto flex flex-wrap items-center gap-3 text-sm">
                    <a
                        href="{{ 'https://twitter.com/intent/tweet?url='.urlencode(request()->fullUrl()).'&text='.urlencode($translation->title) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="border-b border-transparent pb-0.5 text-zinc-600 no-underline hover:border-zinc-400 hover:text-zinc-900"
                    >
                        {{ __('ui.blog.share.x') }}
                    </a>
                    <a
                        href="{{ 'https://www.linkedin.com/sharing/share-offsite/?url='.urlencode(request()->fullUrl()) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="border-b border-transparent pb-0.5 text-zinc-600 no-underline hover:border-zinc-400 hover:text-zinc-900"
                    >
                        {{ __('ui.blog.share.linkedin') }}
                    </a>
                    <a href="{{ request()->fullUrl() }}" class="border-b border-transparent pb-0.5 text-zinc-600 no-underline hover:border-zinc-400 hover:text-zinc-900">
                        {{ __('ui.blog.share.copy') }}
                    </a>
                </div>
            </section>

            <section class="space-y-5">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">{{ __('ui.blog.comments.title') }}</h2>

                @if ($contentItem->show_comments)
                    @auth
                        <x-ui.card>
                            <form method="POST" action="{{ route('content.comments.store', ['contentItem' => $contentItem]) }}" class="space-y-3">
                                @csrf
                                <label for="body_markdown" class="block text-sm font-medium text-zinc-700">{{ __('ui.blog.comments.form_label') }}</label>
                                <textarea
                                    id="body_markdown"
                                    name="body_markdown"
                                    rows="5"
                                    class="w-full border border-zinc-300 px-3 py-2 text-sm"
                                    placeholder="{{ __('ui.blog.comments.form_placeholder') }}"
                                >{{ old('body_markdown') }}</textarea>
                                @error('body_markdown')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="h-10 border border-brand-700 px-4 text-sm font-medium text-brand-700 transition-colors hover:bg-brand-50">
                                    {{ __('ui.blog.comments.publish') }}
                                </button>
                            </form>
                        </x-ui.card>
                    @else
                        <x-ui.alert>{{ __('ui.blog.comments.login') }}</x-ui.alert>
                    @endauth

                    <div class="space-y-4">
                        @forelse ($comments as $comment)
                            <x-ui.card as="article">
                                <div class="space-y-3">
                                    <p class="text-xs text-zinc-500">
                                        {{ $comment->user->name }} • {{ $comment->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="article-content max-w-none text-base">
                                        {!! $renderedComments[$comment->id] !!}
                                    </div>

                                    @can('update', $comment)
                                        <div class="flex flex-wrap items-center gap-4 text-sm">
                                            <form method="POST" action="{{ route('comments.update', ['comment' => $comment]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="is_hidden" value="1">
                                                <button type="submit" class="border-b border-transparent pb-0.5 text-zinc-600 hover:border-zinc-400 hover:text-zinc-900">
                                                    {{ __('ui.blog.comments.hide') }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('comments.destroy', ['comment' => $comment]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="border-b border-transparent pb-0.5 text-zinc-600 hover:border-zinc-400 hover:text-zinc-900">
                                                    {{ __('ui.blog.comments.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    @endcan
                                </div>
                            </x-ui.card>
                        @empty
                            <x-ui.alert>{{ __('ui.blog.comments.empty') }}</x-ui.alert>
                        @endforelse
                    </div>
                @else
                    <x-ui.alert>{{ __('ui.blog.comments.disabled') }}</x-ui.alert>
                @endif
            </section>
        </div>
    </div>
</x-layouts.public>
