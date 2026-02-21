<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.seo-meta')
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">
        <main class="mx-auto max-w-4xl px-6 py-12">
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">{{ __('ui.about.back') }}</a>

            <header class="mt-6">
                <p class="text-xs uppercase tracking-[0.24em] text-zinc-500">{{ config('app.name') }}</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight">{{ __('ui.about.title') }}</h1>
                <p class="mt-3 text-sm leading-6 text-zinc-600">
                    {{ __('ui.about.intro') }}
                </p>
            </header>

            <section class="mt-10 rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="text-xl font-semibold">{{ __('ui.about.summary_title') }}</h2>
                <p class="mt-3 text-sm leading-7 text-zinc-600">
                    {{ __('ui.about.summary_text') }}
                </p>
            </section>

            <section class="mt-10 rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="text-xl font-semibold">{{ __('ui.about.skills_title') }}</h2>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-zinc-600">
                    @foreach (__('ui.about.skills') as $skill)
                        <li>• {{ $skill }}</li>
                    @endforeach
                </ul>
            </section>

            <section class="mt-10 rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="text-xl font-semibold">{{ __('ui.about.experience_title') }}</h2>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-zinc-600">
                    @foreach (__('ui.about.experience') as $experienceLine)
                        <li>• {{ $experienceLine }}</li>
                    @endforeach
                </ul>
            </section>

            <section class="mt-10 rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="text-xl font-semibold">{{ __('ui.about.linkedin_title') }}</h2>
                <p class="mt-3 text-sm leading-6 text-zinc-600">
                    {{ __('ui.about.linkedin_text') }}
                </p>
                <a
                    href="https://www.linkedin.com/in/your-profile"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-4 inline-flex items-center rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100"
                >
                    {{ __('ui.about.linkedin_button') }}
                </a>
            </section>
        </main>
    </body>
</html>
