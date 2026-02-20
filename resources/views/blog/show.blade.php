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

            @if ($contentItem->show_likes)
                <section class="mt-10 rounded-xl border border-zinc-200 p-5">
                    <div class="flex items-center gap-3 text-sm text-zinc-700">
                        <span class="font-medium">Likes: {{ $contentItem->likes_count }}</span>
                        @auth
                            <form method="POST" action="{{ route('content.likes.toggle', ['contentItem' => $contentItem]) }}" class="inline">
                                @csrf
                                <button type="submit" class="rounded border border-zinc-300 px-3 py-1 text-xs hover:bg-zinc-100">
                                    Like / Unlike
                                </button>
                            </form>
                        @endauth
                    </div>
                </section>
            @endif

            <section class="mt-10">
                <h2 class="text-xl font-semibold">Commentaires</h2>

                @if ($contentItem->show_comments)
                    @auth
                        <form method="POST" action="{{ route('content.comments.store', ['contentItem' => $contentItem]) }}" class="mt-4 space-y-3 rounded-xl border border-zinc-200 p-4">
                            @csrf
                            <label for="body_markdown" class="block text-sm font-medium text-zinc-700">Votre commentaire</label>
                            <textarea id="body_markdown" name="body_markdown" rows="4" class="w-full rounded-md border-zinc-300 text-sm">{{ old('body_markdown') }}</textarea>
                            @error('body_markdown')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="rounded border border-zinc-300 px-3 py-1 text-xs hover:bg-zinc-100">Publier</button>
                        </form>
                    @else
                        <p class="mt-4 text-sm text-zinc-600">Connectez-vous pour commenter.</p>
                    @endauth

                    <div class="mt-6 space-y-4">
                        @forelse ($comments as $comment)
                            <article class="rounded-xl border border-zinc-200 p-4">
                                <p class="text-xs text-zinc-500">
                                    {{ $comment->user->name }} • {{ $comment->created_at?->format('Y-m-d H:i') }}
                                </p>
                                <div class="prose prose-sm prose-zinc mt-3 max-w-none">
                                    {!! $renderedComments[$comment->id] !!}
                                </div>

                                @can('update', $comment)
                                    <div class="mt-4 flex items-center gap-2">
                                        <form method="POST" action="{{ route('comments.update', ['comment' => $comment]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_hidden" value="1">
                                            <button type="submit" class="rounded border border-zinc-300 px-2 py-1 text-xs hover:bg-zinc-100">
                                                Masquer
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('comments.destroy', ['comment' => $comment]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded border border-zinc-300 px-2 py-1 text-xs hover:bg-zinc-100">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                @endcan
                            </article>
                        @empty
                            <p class="text-sm text-zinc-600">Aucun commentaire pour le moment.</p>
                        @endforelse
                    </div>
                @else
                    <p class="mt-4 text-sm text-zinc-600">Les commentaires sont désactivés pour cet article.</p>
                @endif
            </section>
        </main>
    </body>
</html>
