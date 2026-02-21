<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-8">
        <header class="space-y-3">
            <h1 class="ui-section-title">{{ __('ui.links.title') }}</h1>
            <p class="max-w-3xl text-base text-zinc-600">{{ __('ui.links.subtitle') }}</p>
        </header>

        @php
            $localeOptions = ['all' => __('ui.blog.filters.locale').' : '.__('ui.blog.filters.all')]
                + collect($supportedLocales)->mapWithKeys(fn (string $locale): array => [$locale => strtoupper($locale)])->all();
        @endphp

        <x-ui.card>
            <form method="GET" action="{{ route('links.index') }}" class="space-y-4">
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                    <label class="space-y-2 text-sm">
                        <span class="block font-medium text-zinc-700">{{ __('ui.blog.filters.search') }}</span>
                        <x-ui.input name="q" :value="$searchTerm" :placeholder="__('ui.blog.filters.search_placeholder')" />
                    </label>

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
