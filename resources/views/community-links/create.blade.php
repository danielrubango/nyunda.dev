<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.seo-meta')
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">
        <main class="mx-auto max-w-3xl px-6 py-12">
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">{{ __('ui.blog.back') }}</a>

            <header class="mt-6">
                <h1 class="text-3xl font-semibold tracking-tight">{{ __('ui.community.create.title') }}</h1>
                <p class="mt-2 text-sm text-zinc-600">
                    {{ __('ui.community.create.subtitle') }}
                </p>
            </header>

            <form method="POST" action="{{ route('community-links.store') }}" class="mt-8 space-y-5 rounded-xl border border-zinc-200 bg-white p-6">
                @csrf

                <div class="space-y-2">
                    <label for="locale" class="block text-sm font-medium text-zinc-700">{{ __('ui.community.fields.locale') }}</label>
                    <select id="locale" name="locale" class="w-full rounded-md border-zinc-300 text-sm">
                        @foreach ($supportedLocales as $locale)
                            <option value="{{ $locale }}" @selected(old('locale', $defaultLocale) === $locale)>{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                    @error('locale')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="title" class="block text-sm font-medium text-zinc-700">{{ __('ui.community.fields.title_optional') }}</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" class="w-full rounded-md border-zinc-300 text-sm" placeholder="{{ __('ui.community.placeholders.title') }}">
                    @error('title')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="excerpt" class="block text-sm font-medium text-zinc-700">{{ __('ui.community.fields.excerpt_optional') }}</label>
                    <textarea id="excerpt" name="excerpt" rows="3" class="w-full rounded-md border-zinc-300 text-sm" placeholder="{{ __('ui.community.placeholders.excerpt') }}">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="external_url" class="block text-sm font-medium text-zinc-700">{{ __('ui.community.fields.external_url') }}</label>
                    <input id="external_url" name="external_url" type="url" value="{{ old('external_url') }}" class="w-full rounded-md border-zinc-300 text-sm">
                    @error('external_url')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="external_site_name" class="block text-sm font-medium text-zinc-700">{{ __('ui.community.fields.external_site_name_optional') }}</label>
                    <input id="external_site_name" name="external_site_name" type="text" value="{{ old('external_site_name') }}" class="w-full rounded-md border-zinc-300 text-sm">
                    @error('external_site_name')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="external_description" class="block text-sm font-medium text-zinc-700">{{ __('ui.community.fields.external_description_optional') }}</label>
                    <textarea id="external_description" name="external_description" rows="3" class="w-full rounded-md border-zinc-300 text-sm">{{ old('external_description') }}</textarea>
                    @error('external_description')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="inline-flex items-center rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">
                    {{ __('ui.community.create.submit') }}
                </button>
            </form>
        </main>
    </body>
</html>
