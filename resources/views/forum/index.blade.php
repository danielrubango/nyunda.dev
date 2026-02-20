<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.seo-meta')
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">
        <main class="mx-auto max-w-5xl px-6 py-12">
            <header class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.24em] text-zinc-500">{{ config('app.name') }}</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight">Forum</h1>
                    <p class="mt-2 text-sm text-zinc-600">Discussions techniques et questions de la communaute.</p>
                </div>
                @auth
                    <a href="{{ route('forum.create') }}" class="inline-flex items-center rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">
                        Nouvelle discussion
                    </a>
                @endauth
            </header>

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <section class="space-y-4">
                @forelse ($threads as $thread)
                    <article class="rounded-xl border border-zinc-200 bg-white p-5">
                        <div class="mb-2 flex flex-wrap items-center gap-2 text-xs uppercase tracking-wide text-zinc-500">
                            <span>{{ strtoupper($thread->locale) }}</span>
                            <span>•</span>
                            <span>{{ $thread->author->name }}</span>
                            <span>•</span>
                            <span>{{ $thread->created_at?->format('Y-m-d H:i') }}</span>
                            <span>•</span>
                            <span>{{ (int) $thread->replies_count }} reponses</span>
                        </div>

                        <a href="{{ route('forum.show', $thread) }}" class="text-xl font-semibold text-zinc-900 hover:text-zinc-700">
                            {{ $thread->title }}
                        </a>

                        <p class="mt-3 text-sm leading-6 text-zinc-600">
                            {{ \Illuminate\Support\Str::limit(strip_tags($thread->body_markdown), 220) }}
                        </p>
                    </article>
                @empty
                    <p class="rounded-xl border border-zinc-200 bg-white p-6 text-sm text-zinc-600">
                        Aucune discussion pour le moment.
                    </p>
                @endforelse
            </section>

            <div class="mt-8">
                {{ $threads->links() }}
            </div>
        </main>
    </body>
</html>
