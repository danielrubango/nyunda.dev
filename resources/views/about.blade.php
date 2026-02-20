<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>About | {{ config('app.name') }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">
        <main class="mx-auto max-w-4xl px-6 py-12">
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">← Retour au blog</a>

            <header class="mt-6">
                <p class="text-xs uppercase tracking-[0.24em] text-zinc-500">{{ config('app.name') }}</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight">About</h1>
                <p class="mt-3 text-sm leading-6 text-zinc-600">
                    Page speciale de presentation, modifiable directement dans ce fichier Blade selon tes besoins.
                </p>
            </header>

            <section class="mt-10 rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="text-xl font-semibold">Resume</h2>
                <p class="mt-3 text-sm leading-7 text-zinc-600">
                    Lead Developer Laravel, focalise sur clean architecture, qualite logicielle, performances,
                    delivery pragmatique et maintenabilite long terme.
                </p>
            </section>

            <section class="mt-10 rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="text-xl font-semibold">Competences</h2>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-zinc-600">
                    <li>• Laravel, Livewire, Filament</li>
                    <li>• Conception de workflows contenu (publication, moderation, i18n)</li>
                    <li>• Tests Pest, refactoring, conventions equipe</li>
                    <li>• APIs, queues, jobs asynchrones, robustesse en production</li>
                </ul>
            </section>

            <section class="mt-10 rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="text-xl font-semibold">Experience</h2>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-zinc-600">
                    <li>• Pilotage technique de projets de MVP a production</li>
                    <li>• Structuration de codebases evolutives pour equipe ou solo</li>
                    <li>• Accompagnement produit avec priorisation orientee impact</li>
                </ul>
            </section>

            <section class="mt-10 rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="text-xl font-semibold">LinkedIn</h2>
                <p class="mt-3 text-sm leading-6 text-zinc-600">
                    Pour un parcours complet et des references professionnelles, consulte mon profil LinkedIn.
                </p>
                <a
                    href="https://www.linkedin.com/in/your-profile"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-4 inline-flex items-center rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100"
                >
                    Ouvrir LinkedIn
                </a>
            </section>
        </main>
    </body>
</html>
