<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-12 sm:space-y-16">
        <section @class([
            'grid gap-8 lg:gap-10',
            'lg:grid-cols-[minmax(0,11fr)_minmax(0,15fr)] lg:items-start' => is_array($featuredRow),
        ])>
            <div class="space-y-6">
                <div class="space-y-4">
                    <p class="ui-eyebrow">{{ __('ui.nav.home') }}</p>
                    <h1 class="max-w-[14ch] text-balance font-sans text-3xl font-semibold tracking-tight text-zinc-900 sm:text-4xl lg:text-5xl">
                        {{ __('ui.home.title') }}
                    </h1>
                    <p class="max-w-[62ch] text-pretty text-base text-zinc-600">
                        {{ __('ui.home.tagline') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-ui.button :href="route('blog.index')" size="lg">
                        {{ __('ui.home.cta_read_blog') }}
                    </x-ui.button>
                    <x-ui.button :href="route('links.index')" size="lg" variant="secondary">
                        {{ __('ui.home.see_all_links') }}
                    </x-ui.button>
                </div>
            </div>

            @if (is_array($featuredRow))
                <div class="space-y-3">
                    <p class="ui-eyebrow">{{ __('ui.home.featured_title') }}</p>

                    <x-public.article-card
                        :item="$featuredRow['content_item']"
                        :translation="$featuredRow['translation']"
                        size="xl"
                        class="border-2 bg-linear-to-br from-white to-brand-50/70 shadow-xs"
                    />
                </div>
            @endif
        </section>

        @if ($recentRows->isNotEmpty())
            <section class="space-y-5">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <h2 class="ui-section-title">{{ __('ui.home.recent_articles_title') }}</h2>
                    <a href="{{ route('blog.index') }}" class="text-sm font-medium text-zinc-700 no-underline hover:text-brand-700">
                        {{ __('ui.home.cta_read_blog') }}
                    </a>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    @foreach ($recentRows as $row)
                        <x-public.article-card :item="$row['content_item']" :translation="$row['translation']" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($popularRows->isNotEmpty())
            <section class="space-y-5">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <h2 class="ui-section-title">{{ __('ui.home.popular_articles_title') }}</h2>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    @foreach ($popularRows as $row)
                        <x-public.article-card
                            :item="$row['content_item']"
                            :translation="$row['translation']"
                            :show-reads="true"
                        />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($linkRows->isNotEmpty())
            <section class="space-y-5">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <h2 class="ui-section-title">{{ __('ui.home.recent_links_title') }}</h2>
                    <a href="{{ route('links.index') }}" class="text-sm font-medium text-zinc-700 no-underline hover:text-brand-700">
                        {{ __('ui.home.see_all_links') }}
                    </a>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    @foreach ($linkRows as $row)
                        <x-public.link-card :item="$row['content_item']" :translation="$row['translation']" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.public>
