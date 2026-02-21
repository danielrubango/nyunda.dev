<footer class="mt-12 border-t border-zinc-200 bg-white">
    <div class="ui-container space-y-6 py-10">
        <x-ui.card class="p-5 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-2">
                    <h2 class="text-lg font-semibold tracking-tight text-zinc-900">{{ __('ui.blog.newsletter.title') }}</h2>
                    <p class="text-sm text-zinc-600">{{ __('ui.blog.newsletter.description') }}</p>
                </div>

                <form method="POST" action="{{ route('newsletter.subscriptions.store') }}" class="grid w-full gap-3 sm:grid-cols-[minmax(0,1fr)_auto] lg:max-w-lg">
                    @csrf
                    <input type="hidden" name="locale" value="{{ old('locale', app()->getLocale()) }}">
                    <x-ui.input
                        type="email"
                        name="email"
                        :value="old('email')"
                        placeholder="you@example.com"
                        required
                    />
                    <button type="submit" class="h-10 border border-brand-700 px-4 text-sm font-medium text-brand-700 transition-colors hover:bg-brand-50">
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

        <div class="flex flex-col gap-3 text-sm text-zinc-600 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ now()->year }} {{ config('app.name') }}</p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('seo.feed') }}" class="no-underline hover:text-brand-700">{{ __('ui.nav.rss') }}</a>
                <a href="{{ route('forum.index') }}" class="no-underline hover:text-brand-700">{{ __('ui.nav.forum') }}</a>
                <a href="{{ route('about.show') }}" class="no-underline hover:text-brand-700">{{ __('ui.nav.about') }}</a>
            </div>
        </div>
    </div>
</footer>
