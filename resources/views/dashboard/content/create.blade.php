<x-layouts.public :title="'Proposer un contenu'">
    <div class="ui-container">
        <div class="mx-auto max-w-3xl space-y-6">
            <header class="space-y-2">
                <h1 class="font-sans text-4xl font-semibold tracking-tight text-zinc-900">Proposer un contenu</h1>
                <p class="text-sm text-zinc-600">Soumettez un article interne, externe ou un lien communautaire depuis votre dashboard.</p>
            </header>

            <x-ui.card>
                <form method="POST" action="{{ route('dashboard.content.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-medium text-zinc-700">
                            Type
                            <select name="type" class="mt-1 w-full border border-zinc-300 px-3 py-2 text-sm" required>
                                <option value="internal_post" @selected(old('type') === 'internal_post')>Article interne</option>
                                <option value="external_post" @selected(old('type') === 'external_post')>Article externe</option>
                                <option value="community_link" @selected(old('type') === 'community_link')>Lien communautaire</option>
                            </select>
                        </label>

                        <label class="block text-sm font-medium text-zinc-700">
                            Langue
                            <select name="locale" class="mt-1 w-full border border-zinc-300 px-3 py-2 text-sm" required>
                                @foreach ($supportedLocales as $locale)
                                    <option value="{{ $locale }}" @selected(old('locale', $defaultLocale) === $locale)>{{ strtoupper($locale) }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <label class="block text-sm font-medium text-zinc-700">
                        Titre
                        <input name="title" type="text" value="{{ old('title') }}" class="mt-1 w-full border border-zinc-300 px-3 py-2 text-sm" required>
                    </label>

                    <label class="block text-sm font-medium text-zinc-700">
                        Extrait (optionnel)
                        <textarea name="excerpt" rows="3" class="mt-1 w-full border border-zinc-300 px-3 py-2 text-sm">{{ old('excerpt') }}</textarea>
                    </label>

                    <label class="block text-sm font-medium text-zinc-700">
                        Corps markdown (obligatoire pour article interne)
                        <textarea name="body_markdown" rows="8" class="mt-1 w-full border border-zinc-300 px-3 py-2 text-sm">{{ old('body_markdown') }}</textarea>
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-medium text-zinc-700">
                            URL externe (obligatoire pour externe/lien)
                            <input name="external_url" type="url" value="{{ old('external_url') }}" class="mt-1 w-full border border-zinc-300 px-3 py-2 text-sm">
                        </label>

                        <label class="block text-sm font-medium text-zinc-700">
                            Site externe (optionnel)
                            <input name="external_site_name" type="text" value="{{ old('external_site_name') }}" class="mt-1 w-full border border-zinc-300 px-3 py-2 text-sm">
                        </label>
                    </div>

                    <label class="block text-sm font-medium text-zinc-700">
                        Description externe (optionnel)
                        <textarea name="external_description" rows="4" class="mt-1 w-full border border-zinc-300 px-3 py-2 text-sm">{{ old('external_description') }}</textarea>
                    </label>

                    <label class="block text-sm font-medium text-zinc-700">
                        URL image de couverture (optionnel)
                        <input name="featured_image_url" type="url" value="{{ old('featured_image_url') }}" class="mt-1 w-full border border-zinc-300 px-3 py-2 text-sm">
                    </label>

                    @if ($errors->any())
                        <div class="space-y-1 border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit" class="inline-flex h-10 items-center border border-brand-700 px-4 text-sm font-medium text-brand-700 transition-colors hover:bg-brand-50">
                        Soumettre
                    </button>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
