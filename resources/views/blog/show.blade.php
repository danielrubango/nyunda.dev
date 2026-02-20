<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $translation->title }} | {{ config('app.name') }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased">
        <main class="mx-auto max-w-3xl px-6 py-12">
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">← Back to blog</a>

            <header class="mt-6 border-b border-zinc-200 pb-6">
                <p class="text-xs uppercase tracking-[0.24em] text-zinc-500">{{ strtoupper($translation->locale) }}</p>
                <h1 class="mt-3 text-4xl font-semibold leading-tight">{{ $translation->title }}</h1>
                <p class="mt-4 text-sm leading-6 text-zinc-600">{{ $translation->excerpt }}</p>
            </header>

            <article class="prose prose-zinc mt-8 max-w-none">
                {!! $renderedBody !!}
            </article>
        </main>
    </body>
</html>
