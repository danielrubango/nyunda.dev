<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-8">
        <header class="space-y-3">
            <p class="ui-eyebrow">{{ config('app.name') }}</p>
            <h1 class="ui-section-title">{{ __('ui.blog.title') }}</h1>
            <p class="max-w-3xl text-base text-zinc-600">{{ __('ui.blog.subtitle') }}</p>
        </header>

        @if (session('status'))
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="error">{{ session('error') }}</x-ui.alert>
        @endif

        @php
            $localeOptions = ['all' => __('ui.blog.filters.all')]
                + collect($supportedLocales)->mapWithKeys(fn (string $locale): array => [$locale => strtoupper($locale)])->all();

            $typeOptions = [
                '' => __('ui.blog.filters.all'),
                'internal_post' => __('ui.blog.filters.internal'),
                'external_post' => __('ui.blog.filters.external'),
            ];

            $tagOptions = ['' => __('ui.blog.filters.all')]
                + $tags->mapWithKeys(fn ($tag): array => [$tag->slug => '#'.$tag->name])->all();
        @endphp

        <x-ui.card>
            <form method="GET" action="{{ route('blog.index') }}" class="grid gap-4 md:grid-cols-5">
                <label class="space-y-2 text-sm md:col-span-2">
                    <span class="block font-medium text-zinc-700">{{ __('ui.blog.filters.search') }}</span>
                    <x-ui.input name="q" :value="$searchTerm" :placeholder="__('ui.blog.filters.search_placeholder')" />
                </label>

                <label class="space-y-2 text-sm">
                    <span class="block font-medium text-zinc-700">{{ __('ui.blog.filters.locale') }}</span>
                    <x-ui.select name="locale" :options="$localeOptions" :selected="$selectedLocale" />
                </label>

                <label class="space-y-2 text-sm">
                    <span class="block font-medium text-zinc-700">{{ __('ui.blog.filters.type') }}</span>
                    <x-ui.select name="type" :options="$typeOptions" :selected="$selectedType" />
                </label>

                <label class="space-y-2 text-sm">
                    <span class="block font-medium text-zinc-700">{{ __('ui.blog.filters.tags') }}</span>
                    <x-ui.select name="tag" :options="$tagOptions" :selected="$selectedTag" />
                </label>

                <div class="flex items-end gap-4 md:col-span-5">
                    <button type="submit" class="border-b border-brand-700 pb-1 text-sm font-medium text-brand-700">
                        {{ __('ui.blog.filters.apply') }}
                    </button>
                    <a href="{{ route('blog.index') }}" class="border-b border-transparent pb-1 text-sm font-medium text-zinc-600 no-underline hover:border-zinc-400 hover:text-zinc-900">
                        {{ __('ui.blog.filters.reset') }}
                    </a>
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
</x-layouts.public>
