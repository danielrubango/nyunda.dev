<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-8">
        <header class="space-y-3">
            <p class="ui-eyebrow">{{ config('app.name') }}</p>
            <h1 class="ui-section-title">{{ __('ui.links.title') }}</h1>
            <p class="max-w-3xl text-base text-zinc-600">{{ __('ui.links.subtitle') }}</p>
        </header>

        @php
            $localeOptions = ['all' => __('ui.blog.filters.all')]
                + collect($supportedLocales)->mapWithKeys(fn (string $locale): array => [$locale => strtoupper($locale)])->all();
        @endphp

        <x-ui.card>
            <form method="GET" action="{{ route('links.index') }}" class="grid gap-4 md:grid-cols-4">
                <label class="space-y-2 text-sm md:col-span-2">
                    <span class="font-medium text-zinc-700">{{ __('ui.blog.filters.search') }}</span>
                    <x-ui.input name="q" :value="$searchTerm" :placeholder="__('ui.blog.filters.search_placeholder')" />
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-medium text-zinc-700">{{ __('ui.blog.filters.locale') }}</span>
                    <x-ui.select name="locale" :options="$localeOptions" :selected="$selectedLocale" />
                </label>

                <div class="flex items-end gap-4">
                    <button type="submit" class="border-b border-brand-700 pb-1 text-sm font-medium text-brand-700">
                        {{ __('ui.blog.filters.apply') }}
                    </button>
                    <a href="{{ route('links.index') }}" class="border-b border-transparent pb-1 text-sm font-medium text-zinc-600 no-underline hover:border-zinc-400 hover:text-zinc-900">
                        {{ __('ui.blog.filters.reset') }}
                    </a>
                </div>
            </form>
        </x-ui.card>

        <section class="grid gap-4 md:grid-cols-2">
            @forelse ($rows as $row)
                @php
                    $item = $row['content_item'];
                    $translation = $row['translation'];
                    $isCommunityLink = $item->type === \App\Enums\ContentType::CommunityLink;
                    $sharedAt = $item->published_at?->format('Y-m-d') ?? $item->created_at?->format('Y-m-d');
                    $host = parse_url((string) $translation->external_url, PHP_URL_HOST);
                    $domain = is_string($host) ? \Illuminate\Support\Str::of($host)->replaceStart('www.', '')->value() : null;
                @endphp

                @if ($isCommunityLink)
                    <x-ui.card
                        as="a"
                        :href="$translation->external_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group overflow-hidden p-0 no-underline md:col-span-2"
                        data-testid="community-link-card"
                        data-layout="full-width"
                    >
                        <div class="flex h-full flex-col gap-4 p-5 sm:p-6">
                            <h3 class="text-2xl font-semibold tracking-tight text-zinc-900 transition-colors group-hover:text-brand-700">
                                <span class="inline-flex items-center gap-2">
                                    <span>{{ $translation->title }}</span>
                                    <x-ui.icon name="external-link" class="size-4 shrink-0 opacity-0 transition-opacity duration-150 group-hover:opacity-100" />
                                </span>
                            </h3>

                            <p class="text-sm text-zinc-600">{{ $translation->external_description ?: $translation->excerpt }}</p>

                            <div class="mt-auto flex items-end justify-between gap-3">
                                <p class="text-xs text-zinc-500">
                                    {{ __('ui.links.shared_on_domain', ['date' => $sharedAt, 'domain' => $domain ?? __('ui.links.unknown_domain')]) }}
                                </p>

                                <div class="flex shrink-0 items-center gap-2">
                                    <x-ui.badge variant="community">
                                        {{ __('ui.blog.content_types.community_link') }}
                                    </x-ui.badge>
                                    <x-ui.badge>{{ strtoupper($translation->locale) }}</x-ui.badge>
                                </div>
                            </div>
                        </div>
                    </x-ui.card>
                @else
                    <x-public.link-card :item="$item" :translation="$translation" />
                @endif
            @empty
                <div class="md:col-span-2">
                    <x-ui.alert>{{ __('ui.links.empty') }}</x-ui.alert>
                </div>
            @endforelse
        </section>

        <x-ui.pagination :paginator="$rows" />
    </div>
</x-layouts.public>
