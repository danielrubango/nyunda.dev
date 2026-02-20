<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Blog | {{ config('app.name') }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">
        <main class="mx-auto max-w-5xl px-6 py-12">
            <header class="mb-8">
                <p class="text-xs uppercase tracking-[0.24em] text-zinc-500">{{ config('app.name') }}</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight">Blog</h1>
                <p class="mt-2 text-sm text-zinc-600">Internal posts, external references, and community links.</p>
                @auth
                    <div class="mt-4">
                        <a href="{{ route('community-links.create') }}" class="inline-flex items-center rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">
                            Soumettre un lien communautaire
                        </a>
                    </div>
                @endauth
            </header>

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('blog.index') }}" class="mb-8 grid gap-4 rounded-xl border border-zinc-200 bg-white p-4 md:grid-cols-3">
                <label class="space-y-2 text-sm">
                    <span class="block font-medium text-zinc-700">Locale</span>
                    <select name="locale" class="w-full rounded-md border-zinc-300 text-sm">
                        <option value="all" @selected($selectedLocale === 'all')>All</option>
                        @foreach ($supportedLocales as $locale)
                            <option value="{{ $locale }}" @selected($selectedLocale === $locale)>{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-2 text-sm">
                    <span class="block font-medium text-zinc-700">Type</span>
                    <select name="type" class="w-full rounded-md border-zinc-300 text-sm">
                        <option value="">All</option>
                        <option value="internal_post" @selected($selectedType === 'internal_post')>Internal</option>
                        <option value="external_post" @selected($selectedType === 'external_post')>External</option>
                        <option value="community_link" @selected($selectedType === 'community_link')>Community</option>
                    </select>
                </label>

                <div class="flex items-end">
                    <button type="submit" class="inline-flex h-10 items-center rounded-md border border-zinc-300 px-4 text-sm font-medium text-zinc-700 hover:bg-zinc-100">
                        Apply filters
                    </button>
                </div>
            </form>

            <section class="space-y-4">
                @forelse ($rows as $row)
                    @php
                        $item = $row['content_item'];
                        $translation = $row['translation'];
                    @endphp

                    <article class="rounded-xl border border-zinc-200 bg-white p-5">
                        <div class="mb-3 flex flex-wrap items-center gap-2 text-xs font-medium uppercase tracking-wide text-zinc-500">
                            <span>{{ str_replace('_', ' ', $item->type->value) }}</span>
                            <span>•</span>
                            <span>{{ strtoupper($translation->locale) }}</span>
                            @if ($item->type->value === 'external_post')
                                <span class="rounded bg-zinc-900 px-2 py-1 text-[10px] tracking-wider text-white">Externe</span>
                            @endif
                        </div>

                        @if ($item->type->value === 'internal_post')
                            <a href="{{ route('blog.show', ['locale' => $translation->locale, 'slug' => $translation->slug]) }}" class="text-xl font-semibold text-zinc-900 hover:text-zinc-700">
                                {{ $translation->title }}
                            </a>
                        @else
                            <a href="{{ $translation->external_url }}" target="_blank" rel="noopener noreferrer" class="text-xl font-semibold text-zinc-900 hover:text-zinc-700">
                                {{ $translation->title }}
                            </a>
                        @endif

                        <p class="mt-3 text-sm leading-6 text-zinc-600">{{ $translation->excerpt }}</p>

                        @if ($item->type->value === 'internal_post' && $item->show_likes)
                            <div class="mt-4 flex items-center gap-3 text-sm text-zinc-700">
                                <span class="font-medium">Likes: {{ (int) ($item->likes_count ?? 0) }}</span>
                                @auth
                                    <form method="POST" action="{{ route('content.likes.toggle', ['contentItem' => $item]) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded border border-zinc-300 px-2 py-1 text-xs hover:bg-zinc-100">
                                            Like / Unlike
                                        </button>
                                    </form>
                                @endauth
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="rounded-xl border border-zinc-200 bg-white p-6 text-sm text-zinc-600">No published content for the selected filters.</p>
                @endforelse
            </section>
        </main>
    </body>
</html>
