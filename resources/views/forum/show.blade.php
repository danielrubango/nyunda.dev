<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.seo-meta')
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased">
        <main class="mx-auto max-w-3xl px-6 py-12">
            <a href="{{ route('forum.index') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">← Retour au forum</a>

            @if (session('status'))
                <div class="mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <header class="mt-6 border-b border-zinc-200 pb-6">
                <p class="text-xs uppercase tracking-[0.24em] text-zinc-500">
                    {{ strtoupper($forumThread->locale) }} • {{ $forumThread->author->name }} • {{ $forumThread->created_at?->format('Y-m-d H:i') }}
                </p>
                <h1 class="mt-3 text-4xl font-semibold leading-tight">{{ $forumThread->title }}</h1>
            </header>

            <article class="prose prose-zinc mt-8 max-w-none">
                {!! $renderedThreadBody !!}
            </article>

            <section id="replies" class="mt-12">
                <h2 class="text-xl font-semibold">Reponses</h2>

                @auth
                    <form method="POST" action="{{ route('forum.replies.store', ['forumThread' => $forumThread]) }}" class="mt-4 space-y-3 rounded-xl border border-zinc-200 p-4">
                        @csrf
                        <label for="body_markdown" class="block text-sm font-medium text-zinc-700">Votre reponse</label>
                        <textarea id="body_markdown" name="body_markdown" rows="5" class="w-full rounded-md border-zinc-300 text-sm">{{ old('body_markdown') }}</textarea>
                        @error('body_markdown')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="rounded border border-zinc-300 px-3 py-1 text-xs hover:bg-zinc-100">
                            Publier
                        </button>
                    </form>
                @else
                    <p class="mt-4 text-sm text-zinc-600">Connectez-vous pour repondre.</p>
                @endauth

                <div class="mt-6 space-y-4">
                    @forelse ($replies as $reply)
                        <article class="rounded-xl border border-zinc-200 p-4 {{ $forumThread->best_reply_id === $reply->id ? 'border-green-300 bg-green-50/40' : '' }}">
                            <div class="flex items-center gap-2 text-xs text-zinc-500">
                                <span>{{ $reply->user->name }}</span>
                                <span>•</span>
                                <span>{{ $reply->created_at?->format('Y-m-d H:i') }}</span>
                                @if ($forumThread->best_reply_id === $reply->id)
                                    <span class="rounded bg-green-600 px-2 py-1 text-[10px] font-medium uppercase tracking-wide text-white">Best reply</span>
                                @endif
                                @if ($reply->is_hidden)
                                    <span class="rounded bg-zinc-900 px-2 py-1 text-[10px] font-medium uppercase tracking-wide text-white">Masquee</span>
                                @endif
                            </div>

                            <div class="prose prose-sm prose-zinc mt-3 max-w-none">
                                {!! $renderedReplies[$reply->id] !!}
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                @can('markBestReply', $forumThread)
                                    @if ($forumThread->best_reply_id !== $reply->id)
                                        <form method="POST" action="{{ route('forum.replies.mark-best', ['forumThread' => $forumThread, 'forumReply' => $reply]) }}">
                                            @csrf
                                            <button type="submit" class="rounded border border-zinc-300 px-2 py-1 text-xs hover:bg-zinc-100">
                                                Marquer best reply
                                            </button>
                                        </form>
                                    @endif
                                @endcan

                                @can('moderate', $reply)
                                    <form method="POST" action="{{ route('forum.replies.update', ['forumReply' => $reply]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_hidden" value="{{ $reply->is_hidden ? '0' : '1' }}">
                                        <button type="submit" class="rounded border border-zinc-300 px-2 py-1 text-xs hover:bg-zinc-100">
                                            {{ $reply->is_hidden ? 'Afficher' : 'Masquer' }}
                                        </button>
                                    </form>
                                @endcan

                                @can('delete', $reply)
                                    <form method="POST" action="{{ route('forum.replies.destroy', ['forumReply' => $reply]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-zinc-300 px-2 py-1 text-xs hover:bg-zinc-100">
                                            Supprimer
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-zinc-600">Aucune reponse pour le moment.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </body>
</html>
