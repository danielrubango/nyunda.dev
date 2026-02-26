<x-layouts.public :title="'Mes contenus'">
    <div class="ui-container">
        <div class="mx-auto max-w-6xl space-y-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 border-b border-transparent pb-1 text-sm font-medium text-zinc-700 no-underline hover:border-zinc-400 hover:text-zinc-900">
                ← Retour au dashboard
            </a>

            <header class="flex flex-wrap items-end justify-between gap-3">
                <div class="space-y-2">
                    <h1 class="font-sans text-4xl font-semibold tracking-tight text-zinc-900">Mes contenus</h1>
                    <p class="text-sm text-zinc-600">Suivez vos articles, leurs statuts et vos indicateurs personnels.</p>
                </div>
                <a href="{{ route('dashboard.content.create') }}" class="inline-flex h-10 items-center border border-brand-700 px-4 text-sm font-medium text-brand-700 no-underline transition-colors hover:bg-brand-50">
                    Nouveau contenu
                </a>
            </header>

            <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <x-ui.card><p class="text-xs text-zinc-500">Total contenus</p><p class="text-2xl font-semibold text-zinc-900">{{ $stats['total'] }}</p></x-ui.card>
                <x-ui.card><p class="text-xs text-zinc-500">Publies</p><p class="text-2xl font-semibold text-zinc-900">{{ $stats['published'] }}</p></x-ui.card>
                <x-ui.card><p class="text-xs text-zinc-500">En attente</p><p class="text-2xl font-semibold text-zinc-900">{{ $stats['pending'] }}</p></x-ui.card>
                <x-ui.card><p class="text-xs text-zinc-500">Rejetes</p><p class="text-2xl font-semibold text-zinc-900">{{ $stats['rejected'] }}</p></x-ui.card>
            </section>

            <section class="grid gap-3 sm:grid-cols-3">
                <x-ui.card><p class="text-xs text-zinc-500">Vues</p><p class="text-xl font-semibold text-zinc-900">{{ number_format($stats['reads']) }}</p></x-ui.card>
                <x-ui.card><p class="text-xs text-zinc-500">Commentaires recus</p><p class="text-xl font-semibold text-zinc-900">{{ number_format($stats['comments']) }}</p></x-ui.card>
                <x-ui.card><p class="text-xs text-zinc-500">Interactions</p><p class="text-xl font-semibold text-zinc-900">{{ number_format($stats['interactions']) }}</p></x-ui.card>
            </section>

            @if (session('status'))
                <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
            @endif

            <x-ui.card>
                <form method="GET" action="{{ route('dashboard.content.index') }}" class="grid gap-3 sm:grid-cols-4">
                    <label class="text-sm font-medium text-zinc-700">
                        Recherche
                        <input name="q" type="text" value="{{ $searchFilter }}" placeholder="Titre ou extrait..." class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm">
                    </label>
                    <label class="text-sm font-medium text-zinc-700">
                        Statut
                        <select name="status" class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm">
                            <option value="">Tous</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected($statusFilter === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-medium text-zinc-700">
                        Type
                        <select name="type" class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm">
                            <option value="">Tous</option>
                            @foreach ($typeOptions as $value => $label)
                                <option value="{{ $value }}" @selected($typeFilter === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex h-10 items-center border border-zinc-300 px-4 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100">Filtrer</button>
                        <a href="{{ route('dashboard.content.index') }}" class="inline-flex h-10 items-center border border-zinc-300 px-4 text-sm font-medium text-zinc-700 no-underline transition-colors hover:bg-zinc-100">Reset</a>
                    </div>
                </form>
            </x-ui.card>

            <x-ui.card :padding="false">
                <div class="overflow-x-auto" role="region" aria-label="datatable">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm" data-test="dashboard-content-datatable">
                        <thead class="bg-zinc-50 text-left text-xs uppercase tracking-wide text-zinc-500">
                            <tr>
                                <th class="px-4 py-3">Titre (cliquable)</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3">Commentaires</th>
                                <th class="px-4 py-3">Interactions</th>
                                <th class="px-4 py-3">Vues</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @forelse ($contentItems as $row)
                                <tr class="hover:bg-zinc-50">
                                    <td class="px-4 py-3 font-medium text-zinc-900">
                                        <a href="{{ $row['edit_url'] }}" class="border-b border-transparent pb-0.5 text-zinc-900 no-underline hover:border-zinc-400 hover:text-zinc-700">
                                            {{ $row['title'] }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700">
                                        <x-ui.badge :variant="$row['status_variant']">{{ $row['status_label'] }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700">{{ $row['comments_count'] }}</td>
                                    <td class="px-4 py-3 text-zinc-700">{{ $row['interaction_count'] }}</td>
                                    <td class="px-4 py-3 text-zinc-700">{{ number_format($row['reads_count']) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ $row['edit_url'] }}" class="inline-flex border border-zinc-300 px-2 py-1 text-xs font-medium text-zinc-700 no-underline transition-colors hover:bg-zinc-100">
                                            Modifier
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-zinc-500">Aucun contenu trouve.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-zinc-200 px-4 py-3">
                    {{ $contentItems->links() }}
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-base font-semibold text-zinc-900">Mes derniers commentaires</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($recentComments as $comment)
                        @php
                            $translation = $comment->contentItem?->translations?->first();
                        @endphp
                        <div class="border border-zinc-200 p-3 text-sm">
                            <p class="font-medium text-zinc-900">{{ $translation?->title ?? 'Contenu supprime' }}</p>
                            <p class="mt-1 text-zinc-700">{{ \Illuminate\Support\Str::limit($comment->body_markdown, 150) }}</p>
                            <p class="mt-1 text-xs text-zinc-500">{{ $comment->created_at?->format('Y-m-d H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">Aucun commentaire recent.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
