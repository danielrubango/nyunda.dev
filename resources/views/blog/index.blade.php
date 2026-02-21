<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-8">
        <header class="space-y-3">
            <h1 class="ui-section-title">{{ __('ui.blog.title') }}</h1>
            <p class="max-w-3xl text-base text-zinc-600">{{ __('ui.blog.subtitle') }}</p>
        </header>

        @php
            $shouldFocusSearch = request()->query('focus') === 'search';
            $localeOptions = ['all' => __('ui.blog.filters.locale').' : '.__('ui.blog.filters.all')]
                + collect($supportedLocales)->mapWithKeys(fn (string $locale): array => [$locale => strtoupper($locale)])->all();

            $typeOptions = [
                '' => __('ui.blog.filters.type'),
                'internal_post' => __('ui.blog.filters.internal'),
                'external_post' => __('ui.blog.filters.external'),
            ];

            $tagOptions = ['' => __('ui.blog.filters.tags')]
                + $tags->mapWithKeys(fn ($tag): array => [$tag->slug => '#'.$tag->name])->all();
        @endphp

        <x-ui.card>
            <form method="GET" action="{{ route('blog.index') }}" class="space-y-4">
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                    <label class="space-y-2 text-sm">
                        <span class="block font-medium text-zinc-700">{{ __('ui.blog.filters.search') }}</span>
                        <x-ui.input
                            id="blog-search-input"
                            name="q"
                            :value="$searchTerm"
                            :placeholder="__('ui.blog.filters.search_placeholder')"
                        />
                    </label>

                    <x-ui.button type="submit" size="lg">
                        {{ __('ui.blog.filters.search_button') }}
                    </x-ui.button>
                </div>

                <div class="grid gap-4 md:grid-cols-4 md:items-end">
                    <x-ui.select name="locale" :options="$localeOptions" :selected="$selectedLocale" class="text-sm" />

                    <x-ui.select name="type" :options="$typeOptions" :selected="$selectedType" class="text-sm" />

                    <x-ui.select name="tag" :options="$tagOptions" :selected="$selectedTag" class="text-sm" />

                    <div class="flex items-end md:justify-end">
                        <a href="{{ route('blog.index', ['reset' => 1]) }}" class="border-b border-transparent pb-1 text-sm font-medium text-zinc-600 no-underline hover:border-zinc-400 hover:text-zinc-900">
                            {{ __('ui.blog.filters.reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </x-ui.card>

        <section class="grid gap-4 md:grid-cols-2">
            @forelse ($rows as $row)
                <x-public.article-card :item="$row['content_item']" :translation="$row['translation']" />
            @empty
                <div class="md:col-span-2">
                    <x-ui.alert>{{ __('ui.blog.empty') }}</x-ui.alert>
                </div>
            @endforelse
        </section>

        <x-ui.pagination :paginator="$rows" />
    </div>

    @if ($shouldFocusSearch)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('blog-search-input');

                if (searchInput instanceof HTMLInputElement) {
                    searchInput.focus();
                }
            });
        </script>
    @endif
</x-layouts.public>
