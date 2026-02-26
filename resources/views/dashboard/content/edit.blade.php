<x-layouts.public :title="'Modifier mon contenu'">
    <div class="ui-container">
        <div class="mx-auto max-w-3xl space-y-6">
            <a href="{{ route('dashboard.content.index') }}" class="inline-flex items-center gap-2 border-b border-transparent pb-1 text-sm font-medium text-zinc-700 no-underline hover:border-zinc-400 hover:text-zinc-900">
                ← Retour a mes contenus
            </a>

            <header class="space-y-2">
                <h1 class="font-sans text-4xl font-semibold tracking-tight text-zinc-900">Modifier mon contenu</h1>
                <p class="text-sm text-zinc-600">Mettez a jour votre contenu sans sortir du dashboard utilisateur.</p>
            </header>

            <x-ui.card>
                @include('dashboard.content._form', [
                    'formAction' => route('dashboard.content.update', ['contentItem' => $contentItem]),
                    'formMethod' => 'PUT',
                    'submitLabel' => 'Enregistrer les modifications',
                    'contentItem' => $contentItem,
                    'translation' => $translation,
                    'supportedLocales' => $supportedLocales,
                    'defaultLocale' => $defaultLocale,
                ])
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
