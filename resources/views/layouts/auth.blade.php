<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', [
            'seo' => ['title' => $title ?? config('app.name')],
        ])
    </head>
    <body class="bg-white text-zinc-900">
        @include('partials.tag-manager-noscript')

        <x-ui.toast-stack />

        <main class="min-h-screen py-8 sm:py-12">
            <div class="ui-container">
                <div class="mx-auto flex min-h-[calc(100svh-8rem)] w-full max-w-4xl items-center justify-center">
                    <div class="w-full max-w-md space-y-5">
                        <div class="flex justify-center">
                            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 text-zinc-900 no-underline">
                                <img src="{{ asset('nyunda-mark.svg') }}" alt="{{ config('app.name') }} logo" class="size-7" />
                                <span class="font-sans text-sm font-semibold tracking-[0.2em]">{{ strtoupper(config('app.name')) }}</span>
                            </a>
                        </div>

                        <x-ui.card class="p-6 sm:p-8">
                            {{ $slot }}
                        </x-ui.card>
                    </div>
                </div>
            </div>
        </main>

        @fluxScripts
    </body>
</html>
