<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-10">
        @if (session('status'))
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="error">{{ session('error') }}</x-ui.alert>
        @endif

        <section class="space-y-4">
            @if (is_array($featuredRow))
                <x-public.article-card
                    :item="$featuredRow['content_item']"
                    :translation="$featuredRow['translation']"
                    size="xl"
                    class="border-2 border-brand-700"
                />
            @else
                <x-ui.alert>{{ __('ui.home.empty_featured') }}</x-ui.alert>
            @endif
        </section>

        <section class="space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <h2 class="ui-section-title">{{ __('ui.home.recent_articles_title') }}</h2>
                <a href="{{ route('blog.index') }}" class="text-sm font-medium text-zinc-700 no-underline hover:text-brand-700">
                    {{ __('ui.home.cta_read_blog') }}
                </a>
            </div>

            @if ($recentRows->isEmpty())
                <x-ui.alert>{{ __('ui.blog.empty') }}</x-ui.alert>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($recentRows as $row)
                        <x-public.article-card :item="$row['content_item']" :translation="$row['translation']" />
                    @endforeach
                </div>
            @endif
        </section>

        <section class="space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <h2 class="ui-section-title">{{ __('ui.home.popular_articles_title') }}</h2>
                <p class="text-sm text-zinc-500">{{ __('ui.home.popular_hint') }}</p>
            </div>

            @if ($popularRows->isEmpty())
                <x-ui.alert>{{ __('ui.home.empty_popular') }}</x-ui.alert>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($popularRows as $row)
                        <x-public.article-card :item="$row['content_item']" :translation="$row['translation']" />
                    @endforeach
                </div>
            @endif
        </section>

        <section class="space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <h2 class="ui-section-title">{{ __('ui.home.recent_links_title') }}</h2>
                <a href="{{ route('links.index') }}" class="text-sm font-medium text-zinc-700 no-underline hover:text-brand-700">
                    {{ __('ui.home.see_all_links') }}
                </a>
            </div>

            @if ($linkRows->isEmpty())
                <x-ui.alert>{{ __('ui.links.empty') }}</x-ui.alert>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($linkRows as $row)
                        <x-public.link-card :item="$row['content_item']" :translation="$row['translation']" />
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts.public>
