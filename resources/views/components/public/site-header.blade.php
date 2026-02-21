@php
    $navigationItems = [
        ['label' => __('ui.nav.blog'), 'route' => 'blog.index', 'active' => ['blog.index', 'blog.show', 'blog.show.localized']],
        ['label' => __('ui.nav.links'), 'route' => 'links.index', 'active' => ['links.index']],
        ['label' => __('ui.nav.forum'), 'route' => 'forum.index', 'active' => ['forum.index', 'forum.show', 'forum.create', 'forum.edit']],
    ];

    $localeOptions = collect(config('app.supported_locales', ['fr', 'en']))
        ->mapWithKeys(fn (string $locale): array => [$locale => strtoupper($locale)])
        ->all();

    $accountUrl = auth()->check() ? route('dashboard') : route('login');
@endphp

<header class="border-b border-zinc-200 bg-white">
    <div class="ui-container flex flex-wrap items-center justify-between gap-4 py-4">
        <a href="{{ route('home') }}" class="font-title text-base font-semibold tracking-[0.22em] text-zinc-900 no-underline">
            {{ strtoupper(config('app.name')) }}
        </a>

        <nav class="flex flex-wrap items-center gap-6" aria-label="{{ __('ui.nav.primary') }}">
            @foreach ($navigationItems as $item)
                @php
                    $isActive = request()->routeIs($item['active']);
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'border-b pb-1 text-sm font-medium no-underline transition-colors',
                        'border-brand-700 text-zinc-900' => $isActive,
                        'border-transparent text-zinc-600 hover:border-zinc-400 hover:text-zinc-900' => ! $isActive,
                    ])
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            <details class="relative">
                <summary class="flex size-9 cursor-pointer items-center justify-center border border-zinc-200 text-zinc-600 list-none hover:text-zinc-900">
                    <x-ui.icon name="search" class="size-4" />
                    <span class="sr-only">{{ __('ui.blog.filters.search') }}</span>
                </summary>
                <div class="absolute right-0 top-11 z-20 w-72 border border-zinc-200 bg-white p-3">
                    <form method="GET" action="{{ route('blog.index') }}" class="space-y-2">
                        <label for="global-search" class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                            {{ __('ui.blog.filters.search') }}
                        </label>
                        <x-ui.input id="global-search" name="q" :placeholder="__('ui.blog.filters.search_placeholder')" />
                    </form>
                </div>
            </details>

            <form method="POST" action="{{ route('locale.update') }}" class="hidden sm:block">
                @csrf
                <label for="site-locale-select" class="sr-only">{{ __('ui.blog.filters.locale') }}</label>
                <x-ui.select
                    id="site-locale-select"
                    name="locale"
                    :options="$localeOptions"
                    :selected="app()->getLocale()"
                    class="h-9 min-w-20 border-zinc-300 px-2 pe-7 text-xs"
                    onchange="this.form.submit()"
                />
            </form>

            <a href="{{ $accountUrl }}" class="border-b border-transparent pb-1 text-sm font-medium text-zinc-700 no-underline transition-colors hover:border-zinc-400 hover:text-zinc-900">
                {{ __('ui.nav.account') }}
            </a>
        </div>
    </div>
</header>
