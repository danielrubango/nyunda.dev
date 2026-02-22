<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-8">
        <header class="space-y-3">
            <h1 class="ui-section-title">{{ __('ui.links.title') }}</h1>
            <p class="max-w-3xl text-base text-zinc-600">{{ __('ui.links.subtitle') }}</p>
        </header>

        @php
            $hasActiveFilters = $searchTerm !== '' || $selectedLocale !== 'all';
            $localeOptions = ['all' => __('ui.blog.filters.locale').' : '.__('ui.blog.filters.all')]
                + collect($supportedLocales)->mapWithKeys(fn (string $locale): array => [$locale => strtoupper($locale)])->all();
        @endphp

        <div x-data="{ filtersOpen: @js($hasActiveFilters) }" x-init="if (window.matchMedia('(min-width: 768px)').matches) { filtersOpen = true }" class="space-y-3">
            <div class="flex md:hidden">
                <button
                    type="button"
                    class="flex size-10 items-center justify-center border border-zinc-300 bg-white text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900"
                    x-on:click="filtersOpen = ! filtersOpen"
                    aria-label="{{ __('ui.blog.filters.search_button') }}"
                >
                    <x-ui.icon name="search" class="size-4" />
                    <span class="sr-only">{{ __('ui.blog.filters.search_button') }}</span>
                </button>
            </div>

            <x-ui.card x-show="filtersOpen" x-transition.opacity.duration.150ms>
                <form method="GET" action="{{ route('links.index') }}" class="space-y-4">
                    <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                        <div class="space-y-2 text-sm">
                            <label for="links-search-input" class="sr-only">{{ __('ui.blog.filters.search') }}</label>
                            <x-ui.input id="links-search-input" name="q" :value="$searchTerm" :placeholder="__('ui.blog.filters.search_placeholder')" />
                        </div>

                        <x-ui.button type="submit" size="lg">
                            {{ __('ui.blog.filters.search_button') }}
                        </x-ui.button>
                    </div>

                    <div class="grid gap-4 md:grid-cols-[minmax(0,320px)_minmax(0,1fr)] md:items-end">
                        <x-ui.select name="locale" :options="$localeOptions" :selected="$selectedLocale" class="text-sm" />

                        <div class="flex items-end md:justify-end">
                            <a href="{{ route('links.index') }}" class="border-b border-transparent pb-1 text-sm font-medium text-zinc-600 no-underline hover:border-zinc-400 hover:text-zinc-900">
                                {{ __('ui.blog.filters.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </x-ui.card>
        </div>

        <section class="grid gap-4 md:grid-cols-2">
            @forelse ($rows as $row)
                <x-public.link-card :item="$row['content_item']" :translation="$row['translation']" />
            @empty
                <div class="md:col-span-2">
                    <x-ui.alert>{{ __('ui.links.empty') }}</x-ui.alert>
                </div>
            @endforelse
        </section>

        <x-ui.pagination :paginator="$rows" />
    </div>
</x-layouts.public>
