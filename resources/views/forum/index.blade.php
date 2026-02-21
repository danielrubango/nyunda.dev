<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div class="space-y-3">
                <p class="ui-eyebrow">{{ config('app.name') }}</p>
                <h1 class="ui-section-title">{{ __('ui.forum.title') }}</h1>
                <p class="max-w-3xl text-base text-zinc-600">{{ __('ui.forum.subtitle') }}</p>
            </div>
            <a href="{{ auth()->check() ? route('forum.create') : route('login') }}" class="border-b border-brand-700 pb-1 text-sm font-medium text-brand-700 no-underline">
                {{ __('ui.forum.ask_question') }}
            </a>
        </header>

        @if (session('status'))
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        @endif

        @php
            $localeOptions = ['all' => __('ui.blog.filters.all')]
                + collect($supportedLocales)->mapWithKeys(fn (string $locale): array => [$locale => strtoupper($locale)])->all();

            $sortOptions = [
                'recent' => __('ui.forum.filters.recent'),
                'active' => __('ui.forum.filters.active'),
                'replies' => __('ui.forum.filters.replies'),
            ];

            $tagOptions = ['' => __('ui.forum.filters.tags_soon')]
                + $availableTags->mapWithKeys(fn ($tag): array => [$tag->slug => '#'.$tag->name])->all();
        @endphp

        <x-ui.card>
            <form method="GET" action="{{ route('forum.index') }}" class="grid gap-4 md:grid-cols-4">
                <label class="space-y-2 text-sm">
                    <span class="font-medium text-zinc-700">{{ __('ui.blog.filters.locale') }}</span>
                    <x-ui.select name="locale" :options="$localeOptions" :selected="$selectedLocale" />
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-medium text-zinc-700">{{ __('ui.forum.filters.sort') }}</span>
                    <x-ui.select name="sort" :options="$sortOptions" :selected="$selectedSort" />
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-medium text-zinc-700">{{ __('ui.blog.filters.tags') }}</span>
                    <x-ui.select name="tag" :options="$tagOptions" :selected="$selectedTag" />
                </label>

                <div class="flex items-end gap-4">
                    <button type="submit" class="border-b border-brand-700 pb-1 text-sm font-medium text-brand-700">
                        {{ __('ui.blog.filters.apply') }}
                    </button>
                    <a href="{{ route('forum.index') }}" class="border-b border-transparent pb-1 text-sm font-medium text-zinc-600 no-underline hover:border-zinc-400 hover:text-zinc-900">
                        {{ __('ui.blog.filters.reset') }}
                    </a>
                </div>
            </form>
        </x-ui.card>

        @if ($selectedTag)
            <x-ui.alert>{{ __('ui.forum.filters.tag_placeholder_notice', ['tag' => $selectedTag]) }}</x-ui.alert>
        @endif

        <section class="space-y-4">
            @forelse ($threads as $thread)
                <x-ui.card as="a" href="{{ route('forum.show', $thread) }}" class="group flex h-full flex-col no-underline">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-wide text-zinc-500">
                            <span>{{ strtoupper($thread->locale) }}</span>
                            <span>•</span>
                            <span>{{ $thread->author->name }}</span>
                            <span>•</span>
                            <span>{{ $thread->created_at?->format('Y-m-d H:i') }}</span>
                            <span>•</span>
                            <span>{{ __('ui.forum.replies_count', ['count' => (int) $thread->replies_count]) }}</span>
                        </div>

                        <h2 class="text-2xl font-semibold tracking-tight text-zinc-900 transition-colors group-hover:text-brand-700">
                            {{ $thread->title }}
                        </h2>

                        <p class="mt-auto max-w-[75ch] text-sm text-zinc-600">{{ \Illuminate\Support\Str::limit(strip_tags($thread->body_markdown), 220) }}</p>
                    </div>
                </x-ui.card>
            @empty
                <x-ui.alert>{{ __('ui.forum.no_threads') }}</x-ui.alert>
            @endforelse
        </section>

        <x-ui.pagination :paginator="$threads" />
    </div>
</x-layouts.public>
