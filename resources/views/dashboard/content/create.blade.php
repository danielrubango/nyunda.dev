<x-layouts.public :title="'Proposer un contenu'">
    <div class="ui-container">
        <div class="mx-auto max-w-3xl space-y-6">
            <a href="{{ route('dashboard.content.index') }}" class="inline-flex items-center gap-2 border-b border-transparent pb-1 text-sm font-medium text-zinc-700 no-underline hover:border-zinc-400 hover:text-zinc-900">
                ← Retour a mes contenus
            </a>

            <header class="space-y-2">
                <h1 class="font-sans text-4xl font-semibold tracking-tight text-zinc-900">Proposer un contenu</h1>
                <p class="text-sm text-zinc-600">Soumettez un article interne, externe ou un lien communautaire depuis votre dashboard.</p>
            </header>

            <x-ui.card>
                @include('dashboard.content._form', [
                    'formAction' => route('dashboard.content.store'),
                    'formMethod' => 'POST',
                    'submitLabel' => 'Soumettre',
                    'contentItem' => null,
                    'translation' => null,
                    'supportedLocales' => $supportedLocales,
                    'defaultLocale' => $defaultLocale,
                ])
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
