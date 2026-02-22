@php
    $localeOptions = collect(config('app.supported_locales', ['fr', 'en']))
        ->mapWithKeys(fn (string $locale): array => [$locale => strtoupper($locale)])
        ->all();
@endphp

<footer class="mt-12 border-t border-zinc-200 bg-white">
    <div class="ui-container space-y-6 py-10">
        <x-ui.card class="p-5 sm:p-6">
            <div class="flex h-full flex-col gap-4">
                <div class="space-y-2">
                    <h2 class="text-lg font-semibold tracking-tight text-zinc-900">{{ __('ui.blog.newsletter.title') }}</h2>
                    <p class="text-sm text-zinc-600">{{ __('ui.blog.newsletter.description') }}</p>
                </div>

                <form method="POST" action="{{ route('newsletter.subscriptions.store') }}" class="mt-auto grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">
                    @csrf
                    <input type="hidden" name="locale" value="{{ old('locale', app()->getLocale()) }}">
                    <x-ui.input
                        type="email"
                        name="email"
                        :value="old('email')"
                        placeholder="you@example.com"
                        required
                    />
                    <button type="submit" class="h-10 w-fit border border-brand-700 bg-brand-700 px-4 text-sm font-medium text-white transition-colors hover:bg-brand-800 focus:border-brand-800 focus-visible:border-brand-800">
                        {{ __('ui.blog.newsletter.submit') }}
                    </button>
                </form>
            </div>

            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('locale')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </x-ui.card>

        <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between">
            <p class="text-zinc-600">© {{ now()->year }} {{ config('app.name') }}</p>

            <form method="POST" action="{{ route('locale.update') }}" class="sm:hidden">
                @csrf
                <label for="footer-locale-select" class="sr-only">{{ __('ui.blog.filters.locale') }}</label>
                <x-ui.select
                    id="footer-locale-select"
                    name="locale"
                    :options="$localeOptions"
                    :selected="app()->getLocale()"
                    class="h-9 min-w-20 border-zinc-300 px-2 pe-7 text-xs"
                    onchange="this.form.submit()"
                />
            </form>

            <nav class="flex flex-wrap items-center gap-4 text-zinc-700">
                <a href="{{ route('seo.feed') }}" class="no-underline hover:text-brand-700">{{ __('ui.nav.rss') }}</a>
                <a href="{{ route('forum.index') }}" class="no-underline hover:text-brand-700">{{ __('ui.nav.forum') }}</a>
                <a href="{{ route('about.show') }}" class="no-underline hover:text-brand-700">{{ __('ui.nav.about') }}</a>
            </nav>
        </div>
    </div>
</footer>
