@php use App\Enums\UserRole; @endphp

@php
    $navigationItems = [
        ['label' => __('ui.nav.blog'), 'route' => 'blog.index', 'active' => ['blog.index', 'blog.show', 'blog.show.localized'], 'disabled' => false],
        ['label' => __('ui.nav.links'), 'route' => 'links.index', 'active' => ['links.index'], 'disabled' => false],
        ['label' => __('ui.nav.forum'), 'route' => 'forum.index', 'active' => ['forum.index', 'forum.show', 'forum.create', 'forum.edit'], 'disabled' => true],
    ];

    $localeOptions = collect(config('app.supported_locales', ['fr', 'en']))
        ->mapWithKeys(fn (string $locale): array => [$locale => strtoupper($locale)])
        ->all();

    $authenticatedUser = auth()->user();
    $isAuthenticated = $authenticatedUser !== null;
    $isAdmin = $authenticatedUser?->hasRole(UserRole::Admin) ?? false;
@endphp

<header class="border-b border-zinc-200 bg-white">
    <div class="ui-container flex flex-wrap items-center justify-between gap-4 py-4 md:flex-nowrap">
        <a href="{{ route('home') }}" class="order-1 inline-flex items-center gap-2 font-title text-base font-semibold tracking-[0.22em] text-zinc-900 no-underline">
            <img src="{{ asset('nyunda-mark.svg') }}" alt="{{ config('app.name') }} logo" class="size-7 shrink-0" />
            <span>{{ strtoupper(config('app.name')) }}</span>
        </a>

        <nav class="order-3 flex basis-full flex-wrap items-center gap-6 md:order-2 md:basis-auto" aria-label="{{ __('ui.nav.primary') }}">
            @foreach ($navigationItems as $item)
                @php
                    $isActive = request()->routeIs($item['active']);
                    $isDisabled = (bool) $item['disabled'];
                @endphp
                @if ($isDisabled)
                    <span class="inline-flex items-center gap-2 pb-1 text-xs font-semibold tracking-[0.12em] uppercase text-zinc-400">
                        <span>{{ $item['label'] }}</span>
                        <x-ui.badge variant="external">{{ __('ui.forum.coming_soon_badge') }}</x-ui.badge>
                    </span>
                @else
                    <a
                        href="{{ route($item['route']) }}"
                        @class([
                            'border-b pb-1 text-xs tracking-[0.12em] uppercase no-underline transition-colors',
                            'border-brand-700 font-bold text-zinc-900' => $isActive,
                            'border-transparent font-semibold text-zinc-600 hover:border-zinc-400 hover:text-zinc-900' => ! $isActive,
                        ])
                    >
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="order-2 flex items-center gap-3 md:order-3">
            <a
                href="{{ route('blog.index', ['focus' => 'search']) }}"
                class="flex size-9 items-center justify-center border border-zinc-200 text-zinc-600 no-underline transition-colors hover:text-zinc-900"
            >
                <x-ui.icon name="search" class="size-4" />
                <span class="sr-only">{{ __('ui.blog.filters.search') }}</span>
            </a>

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

            @if ($isAuthenticated)
                <details class="group relative">
                    <summary class="inline-flex list-none cursor-pointer items-center gap-1 border-b border-transparent pb-1 text-sm font-medium text-zinc-700 transition-colors hover:border-zinc-400 hover:text-zinc-900 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('ui.nav.account') }}</span>
                        <x-ui.icon name="chevron-down" class="size-4 transition-transform group-open:rotate-180" />
                    </summary>

                    <div class="absolute right-0 top-8 z-30 min-w-40 border border-zinc-200 bg-white p-2 text-sm shadow-xs">
                        @if ($isAdmin)
                            <a href="{{ route('dashboard') }}" class="block px-2 py-1.5 text-zinc-700 no-underline transition-colors hover:bg-zinc-100 hover:text-zinc-900">
                                {{ __('ui.nav.dashboard') }}
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-2 py-1.5 text-left text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900">
                                {{ __('ui.nav.logout') }}
                            </button>
                        </form>
                    </div>
                </details>
            @else
                <a href="{{ route('login') }}" class="border-b border-transparent pb-1 text-sm font-medium text-zinc-700 no-underline transition-colors hover:border-zinc-400 hover:text-zinc-900">
                    {{ __('ui.nav.account') }}
                </a>
            @endif
        </div>
    </div>
</header>
