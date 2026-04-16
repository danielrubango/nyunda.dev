<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.seo-meta')
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased">
        @include('partials.tag-manager-noscript')

        <main class="mx-auto max-w-3xl px-6 py-16">
            <p class="text-xs uppercase tracking-[0.24em] text-zinc-500">{{ config('app.name') }}</p>
            <h1 class="mt-4 text-4xl font-semibold leading-tight">{{ $user->name }}</h1>

            @if ($user->headline)
                <p class="mt-3 text-lg text-zinc-700">{{ $user->headline }}</p>
            @endif

            @if ($user->location)
                <p class="mt-2 text-sm text-zinc-600">{{ $user->location }}</p>
            @endif

            @if ($user->bio)
                <section class="mt-8 space-y-2">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Bio</h2>
                    <p class="whitespace-pre-line text-sm text-zinc-700">{{ $user->bio }}</p>
                </section>
            @endif

            @if ($user->website_url || $user->linkedin_url || $user->x_url || $user->github_url)
                <section class="mt-8 space-y-2">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Liens</h2>
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        @if ($user->website_url)
                            <a href="{{ $user->website_url }}" target="_blank" rel="noopener noreferrer" class="text-zinc-700 no-underline hover:text-zinc-900">Site web</a>
                        @endif
                        @if ($user->linkedin_url)
                            <a href="{{ $user->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="text-zinc-700 no-underline hover:text-zinc-900">LinkedIn</a>
                        @endif
                        @if ($user->x_url)
                            <a href="{{ $user->x_url }}" target="_blank" rel="noopener noreferrer" class="text-zinc-700 no-underline hover:text-zinc-900">X</a>
                        @endif
                        @if ($user->github_url)
                            <a href="{{ $user->github_url }}" target="_blank" rel="noopener noreferrer" class="text-zinc-700 no-underline hover:text-zinc-900">GitHub</a>
                        @endif
                    </div>
                </section>
            @endif
        </main>
    </body>
</html>
