@php
    $selectedType = old('type', $contentItem?->type?->value ?? 'internal_post');
    $selectedLocale = old('locale', $translation?->locale ?? $defaultLocale);
@endphp

<form method="POST" action="{{ $formAction }}" class="space-y-4">
    @csrf
    @if ($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-sm font-medium text-zinc-700">
            Type
            <select name="type" class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm" required>
                <option value="internal_post" @selected($selectedType === 'internal_post')>Article interne</option>
                <option value="external_post" @selected($selectedType === 'external_post')>Article externe</option>
                <option value="community_link" @selected($selectedType === 'community_link')>Lien communautaire</option>
            </select>
        </label>

        <label class="block text-sm font-medium text-zinc-700">
            Langue
            <select name="locale" class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm" required>
                @foreach ($supportedLocales as $locale)
                    <option value="{{ $locale }}" @selected($selectedLocale === $locale)>{{ strtoupper($locale) }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <label class="block text-sm font-medium text-zinc-700">
        Titre
        <input
            name="title"
            type="text"
            value="{{ old('title', $translation?->title ?? '') }}"
            class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm"
            placeholder="Ex: Comprendre les jobs queues avec Laravel"
            required
        >
    </label>

    <label class="block text-sm font-medium text-zinc-700">
        Extrait (optionnel)
        <textarea
            name="excerpt"
            rows="3"
            class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm"
            placeholder="Resume court affiche dans les cartes et listes"
        >{{ old('excerpt', $translation?->excerpt ?? '') }}</textarea>
    </label>

    <x-dashboard.markdown-editor
        name="body_markdown"
        :value="old('body_markdown', $translation?->body_markdown ?? '')"
        label="Corps markdown (obligatoire pour article interne)"
        placeholder="# Titre\n\nVotre contenu en markdown ici..."
    />

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-sm font-medium text-zinc-700">
            URL externe (obligatoire pour externe/lien)
            <input
                name="external_url"
                type="url"
                value="{{ old('external_url', $translation?->external_url ?? '') }}"
                class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm"
                placeholder="https://exemple.com/article"
            >
        </label>

        <label class="block text-sm font-medium text-zinc-700">
            Site externe (optionnel)
            <input
                name="external_site_name"
                type="text"
                value="{{ old('external_site_name', $translation?->external_site_name ?? '') }}"
                class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm"
                placeholder="Nom du site source"
            >
        </label>
    </div>

    <label class="block text-sm font-medium text-zinc-700">
        Description externe (optionnel)
        <textarea
            name="external_description"
            rows="4"
            class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm"
            placeholder="Description utile pour les contenus externes"
        >{{ old('external_description', $translation?->external_description ?? '') }}</textarea>
    </label>

    <label class="block text-sm font-medium text-zinc-700">
        URL image de couverture (optionnel)
        <input
            name="featured_image_url"
            type="url"
            value="{{ old('featured_image_url', $translation?->featured_image_url ?? '') }}"
            class="mt-1 w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm"
            placeholder="https://exemple.com/image.jpg"
        >
    </label>

    @if ($errors->any())
        <div class="space-y-1 rounded-sm border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="inline-flex h-10 items-center border border-brand-700 px-4 text-sm font-medium text-brand-700 transition-colors hover:bg-brand-50">
            {{ $submitLabel }}
        </button>
    </div>
</form>
