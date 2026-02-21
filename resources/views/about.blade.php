<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-8">
        <header class="max-w-3xl space-y-3">
            <h1 class="ui-section-title">{{ __('ui.about.title') }}</h1>
            <p class="text-lg text-zinc-600">{{ __('ui.about.intro') }}</p>
        </header>

        <section class="max-w-[80ch] space-y-5">
            <p>{{ __('ui.about.paragraph_1') }}</p>
            <p>{{ __('ui.about.paragraph_2') }}</p>
            <p>{{ __('ui.about.paragraph_3') }}</p>
        </section>

        <section class="flex flex-wrap items-center gap-4 text-sm">
            @foreach ($socialLinks as $socialLink)
                <a href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer" class="border-b border-transparent pb-0.5 font-medium text-zinc-700 no-underline hover:border-zinc-400 hover:text-zinc-900">
                    <span class="inline-flex items-center gap-1.5">
                        <x-ui.icon :name="$socialLink['icon']" class="size-4" />
                        <span>{{ $socialLink['label'] }}</span>
                    </span>
                </a>
            @endforeach
        </section>
    </div>
</x-layouts.public>
