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
            <p class="mt-4 text-sm text-zinc-600">{{ $user->email }}</p>
        </main>
    </body>
</html>
