<x-layouts.public :title="'Mes contenus'">
    <div class="ui-container">
        <div class="mx-auto w-full max-w-6xl space-y-6">
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
                <x-ui.card><p class="text-xs text-zinc-500">Publies</p><p class="text-2xl font-semibold text-zinc-900">{{ $stats['published'] }}</p></x-ui.card>
                <x-ui.card><p class="text-xs text-zinc-500">En attente</p><p class="text-2xl font-semibold text-zinc-900">{{ $stats['pending'] }}</p></x-ui.card>
                <x-ui.card><p class="text-xs text-zinc-500">Rejetes</p><p class="text-2xl font-semibold text-zinc-900">{{ $stats['rejected'] }}</p></x-ui.card>
                <x-ui.card><p class="text-xs text-zinc-500">Vues</p><p class="text-2xl font-semibold text-zinc-900">{{ number_format($stats['reads']) }}</p></x-ui.card>
            </section>

            @if (session('status'))
                <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
            @endif

            <x-ui.card>
                <form method="GET" action="{{ route('dashboard.content.index') }}" class="grid gap-3 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
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
                        <button type="submit" class="inline-flex h-10 items-center border border-brand-700 px-4 text-sm font-medium text-brand-700 transition-colors hover:bg-brand-50">Filtrer</button>
                        <a href="{{ route('dashboard.content.index') }}" class="inline-flex h-10 items-center border border-zinc-300 px-4 text-sm font-medium text-zinc-700 no-underline transition-colors hover:bg-zinc-100">Reset</a>
                    </div>
                </form>
            </x-ui.card>

            @php
                $sortIndicator = static function (string $column, string $sort, string $sortDirection): string {
                    if ($sort !== $column) {
                        return '↕';
                    }

                    return $sortDirection === 'asc' ? '↑' : '↓';
                };

                $sortDirectionFor = static function (string $column, string $sort, string $sortDirection): string {
                    if ($sort === $column) {
                        return $sortDirection === 'asc' ? 'desc' : 'asc';
                    }

                    return 'asc';
                };

                $sortUrl = static function (string $column) use ($sort, $sortDirection, $sortDirectionFor): string {
                    $query = array_merge(request()->query(), [
                        'sort' => $column,
                        'direction' => $sortDirectionFor($column, $sort, $sortDirection),
                        'page' => 1,
                    ]);

                    return route('dashboard.content.index', $query);
                };
            @endphp

            <x-ui.card :padding="false" class="w-full">
                <div class="w-full overflow-x-auto" role="region" aria-label="datatable">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm" data-test="dashboard-content-datatable">
                        <thead class="bg-zinc-50 text-left text-xs uppercase tracking-wide text-zinc-500">
                            <tr>
                                <th class="px-4 py-3">
                                    <a href="{{ $sortUrl('title') }}" class="inline-flex items-center gap-2 text-zinc-600 no-underline hover:text-zinc-900">
                                        <span>Titre</span>
                                        <span>{{ $sortIndicator('title', $sort, $sortDirection) }}</span>
                                    </a>
                                </th>
                                <th class="px-4 py-3">
                                    <a href="{{ $sortUrl('status') }}" class="inline-flex items-center gap-2 text-zinc-600 no-underline hover:text-zinc-900">
                                        <span>Statut</span>
                                        <span>{{ $sortIndicator('status', $sort, $sortDirection) }}</span>
                                    </a>
                                </th>
                                <th class="px-4 py-3">
                                    <a href="{{ $sortUrl('comments') }}" class="inline-flex items-center gap-2 text-zinc-600 no-underline hover:text-zinc-900">
                                        <span>Commentaires</span>
                                        <span>{{ $sortIndicator('comments', $sort, $sortDirection) }}</span>
                                    </a>
                                </th>
                                <th class="px-4 py-3">
                                    <a href="{{ $sortUrl('interactions') }}" class="inline-flex items-center gap-2 text-zinc-600 no-underline hover:text-zinc-900">
                                        <span>Interactions</span>
                                        <span>{{ $sortIndicator('interactions', $sort, $sortDirection) }}</span>
                                    </a>
                                </th>
                                <th class="px-4 py-3">
                                    <a href="{{ $sortUrl('reads') }}" class="inline-flex items-center gap-2 text-zinc-600 no-underline hover:text-zinc-900">
                                        <span>Vues</span>
                                        <span>{{ $sortIndicator('reads', $sort, $sortDirection) }}</span>
                                    </a>
                                </th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @forelse ($contentItems as $row)
                                <tr
                                    class="cursor-pointer hover:bg-zinc-50"
                                    onclick="if (!event.target.closest('a,button,input,select,textarea,label,summary,details,form')) { window.location.href = '{{ $row['edit_url'] }}'; }"
                                    onkeydown="if ((event.key === 'Enter' || event.key === ' ') && !event.target.closest('a,button,input,select,textarea,label,summary,details,form')) { event.preventDefault(); window.location.href = '{{ $row['edit_url'] }}'; }"
                                    tabindex="0"
                                    role="link"
                                    aria-label="Modifier {{ $row['title'] }}"
                                    data-row-link="{{ $row['edit_url'] }}"
                                >
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
                <div class="mt-2 space-y-1.5">
                    @forelse ($recentComments as $comment)
                        @php
                            $contentItem = $comment->contentItem;
                            $translation = $contentItem?->translations?->first();
                            $commentUrl = null;

                            if ($contentItem?->isInternalPost() === true && $contentItem->isPublished() && $translation !== null) {
                                $commentUrl = route('blog.show', [
                                    'locale' => $translation->locale,
                                    'slug' => $translation->slug,
                                ]).'#comment-'.$comment->id;
                            }
                        @endphp

                        <div class="border border-zinc-200 px-3 py-2 text-sm">
                            <p class="font-medium text-zinc-900">
                                @if ($commentUrl !== null)
                                    <a href="{{ $commentUrl }}" class="text-zinc-900 no-underline hover:text-brand-700">{{ $translation?->title ?? 'Contenu indisponible' }}</a>
                                @else
                                    {{ $translation?->title ?? 'Contenu indisponible' }}
                                @endif
                            </p>
                            <p class="mt-0.5 text-zinc-700">
                                @if ($commentUrl !== null)
                                    <a href="{{ $commentUrl }}" class="text-zinc-700 no-underline hover:text-brand-700">{{ \Illuminate\Support\Str::limit($comment->body_markdown, 120) }}</a>
                                @else
                                    {{ \Illuminate\Support\Str::limit($comment->body_markdown, 120) }}
                                @endif
                            </p>
                            <p class="mt-0.5 text-xs text-zinc-500">{{ $comment->created_at?->format('Y-m-d H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">Aucun commentaire recent.</p>
                    @endforelse
                </div>

                @if ($recentComments->hasPages())
                    <div class="mt-3 border-t border-zinc-200 pt-3">
                        {{ $recentComments->links() }}
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
