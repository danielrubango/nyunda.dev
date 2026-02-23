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

<header class="border-b border-zinc-200 bg-white font-sans">
    <div class="ui-container flex flex-wrap items-center justify-between gap-4 py-4 md:flex-nowrap">
        <a href="{{ route('home') }}" class="order-1 inline-flex items-center gap-2 font-sans text-base font-semibold tracking-[0.22em] text-zinc-900 no-underline">
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
                @if (request()->routeIs('blog.show'))
                    <input type="hidden" name="current_content_locale" value="{{ (string) request()->route('locale') }}">
                    <input type="hidden" name="current_content_slug" value="{{ (string) request()->route('slug') }}">
                @endif
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
                <flux:dropdown position="bottom" align="end" class="relative">
                    <button
                        type="button"
                        class="inline-flex cursor-pointer items-center gap-1 border-b border-transparent pb-1 text-sm font-medium text-zinc-700 transition-colors hover:border-zinc-400 hover:text-zinc-900"
                        data-test="public-account-menu-button"
                    >
                        <span class="inline-flex size-6 items-center justify-center rounded-full border border-zinc-300 bg-zinc-100 text-[0.65rem] font-semibold uppercase tracking-wide text-zinc-700">
                            {{ $authenticatedUser->initials() }}
                        </span>
                        <span>{{ __('ui.nav.account') }}</span>
                        <x-ui.icon name="chevron-down" class="size-4" />
                    </button>

                    <flux:menu class="min-w-44">
                        @if ($isAdmin)
                            <flux:menu.item :href="route('dashboard')">
                                {{ __('ui.nav.dashboard') }}
                            </flux:menu.item>
                        @endif

                        <flux:menu.item :href="route('profile.edit')">
                            {{ __('ui.nav.settings') }}
                        </flux:menu.item>

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" class="w-full">
                                {{ __('ui.nav.logout') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @else
                <a href="{{ route('login') }}" class="border-b border-transparent pb-1 text-sm font-medium text-zinc-700 no-underline transition-colors hover:border-zinc-400 hover:text-zinc-900">
                    {{ __('ui.nav.account') }}
                </a>
            @endif
        </div>
    </div>
</header>
