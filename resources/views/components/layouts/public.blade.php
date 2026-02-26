@props([
    'seo' => null,
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', [
            'seo' => $seo ?? ['title' => $title ?? config('app.name')],
        ])
    </head>
    <body class="bg-white text-zinc-900">
        @include('partials.tag-manager-noscript')

        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-50 focus:rounded-sm focus:bg-white focus:px-3 focus:py-2 focus:text-sm focus:font-medium"
        >
            {{ __('ui.accessibility.skip_to_content') }}
        </a>

        <div class="flex min-h-screen flex-col">
            <x-public.site-header />
            <x-ui.toast-stack />

            <main id="main-content" class="flex-1 py-10 sm:py-14">
                {{ $slot }}
            </main>

            <x-public.site-footer />
        </div>
        @stack('scripts')
        @fluxScripts
    </body>
</html>
