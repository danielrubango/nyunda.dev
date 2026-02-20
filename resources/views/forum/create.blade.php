<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.seo-meta')
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">
        <main class="mx-auto max-w-3xl px-6 py-12">
            <a href="{{ route('forum.index') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">← Retour au forum</a>

            <header class="mt-6">
                <h1 class="text-3xl font-semibold tracking-tight">Nouvelle discussion</h1>
                <p class="mt-2 text-sm text-zinc-600">Expose ton probleme ou ton sujet de discussion.</p>
            </header>

            <form method="POST" action="{{ route('forum.store') }}" class="mt-8 space-y-5 rounded-xl border border-zinc-200 bg-white p-6">
                @csrf

                <div class="space-y-2">
                    <label for="locale" class="block text-sm font-medium text-zinc-700">Langue</label>
                    <select id="locale" name="locale" class="w-full rounded-md border-zinc-300 text-sm">
                        @foreach ($supportedLocales as $locale)
                            <option value="{{ $locale }}" @selected(old('locale', $defaultLocale) === $locale)>{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                    @error('locale')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="title" class="block text-sm font-medium text-zinc-700">Titre</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" class="w-full rounded-md border-zinc-300 text-sm" required>
                    @error('title')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="body_markdown" class="block text-sm font-medium text-zinc-700">Description</label>
                    <textarea id="body_markdown" name="body_markdown" rows="10" class="w-full rounded-md border-zinc-300 text-sm" required>{{ old('body_markdown') }}</textarea>
                    @error('body_markdown')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="inline-flex items-center rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">
                    Publier la discussion
                </button>
            </form>
        </main>
    </body>
</html>
