<x-layouts.public :seo="$seo">
    <div class="ui-container space-y-8">
        <header class="max-w-3xl space-y-3">
            <p class="ui-eyebrow">{{ config('app.name') }}</p>
            <h1 class="ui-section-title">{{ __('ui.about.title') }}</h1>
            <p class="text-lg text-zinc-600">{{ __('ui.about.intro') }}</p>
        </header>

        <x-ui.card>
            <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">{{ __('ui.about.summary_title') }}</h2>
            <p class="mt-4 max-w-[75ch]">{{ __('ui.about.summary_text') }}</p>
        </x-ui.card>

        <section class="grid gap-4 lg:grid-cols-2">
            <x-ui.card>
                <h2 class="text-xl font-semibold tracking-tight text-zinc-900">{{ __('ui.about.projects_title') }}</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($projects as $project)
                        <article class="border border-zinc-200 p-4">
                            <h3 class="text-base font-semibold text-zinc-900">{{ $project->name }}</h3>
                            @if ($project->description)
                                <p class="mt-1 text-sm text-zinc-600">{{ $project->description }}</p>
                            @endif
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block border-b border-transparent pb-0.5 text-sm font-medium text-zinc-700 no-underline hover:border-zinc-400 hover:text-zinc-900">
                                    {{ __('ui.about.visit_project') }}
                                </a>
                            @endif
                        </article>
                    @empty
                        <x-ui.alert>{{ __('ui.about.empty_projects') }}</x-ui.alert>
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-xl font-semibold tracking-tight text-zinc-900">{{ __('ui.about.tools_title') }}</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($tools as $tool)
                        <article class="border border-zinc-200 p-4">
                            <h3 class="text-base font-semibold text-zinc-900">{{ $tool->name }}</h3>
                            @if ($tool->description)
                                <p class="mt-1 text-sm text-zinc-600">{{ $tool->description }}</p>
                            @endif
                            @if ($tool->url)
                                <a href="{{ $tool->url }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block border-b border-transparent pb-0.5 text-sm font-medium text-zinc-700 no-underline hover:border-zinc-400 hover:text-zinc-900">
                                    {{ __('ui.about.visit_tool') }}
                                </a>
                            @endif
                        </article>
                    @empty
                        <x-ui.alert>{{ __('ui.about.empty_tools') }}</x-ui.alert>
                    @endforelse
                </div>
            </x-ui.card>
        </section>

        <x-ui.card>
            <h2 class="text-xl font-semibold tracking-tight text-zinc-900">{{ __('ui.about.social_title') }}</h2>
            <p class="mt-3 text-sm text-zinc-600">{{ __('ui.about.linkedin_text') }}</p>
            <div class="mt-4 flex flex-wrap items-center gap-4 text-sm">
                @foreach ($socialLinks as $socialLink)
                    <a href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer" class="border-b border-transparent pb-0.5 font-medium text-zinc-700 no-underline hover:border-zinc-400 hover:text-zinc-900">
                        {{ $socialLink['label'] }}
                    </a>
                @endforeach
            </div>
        </x-ui.card>
    </div>
</x-layouts.public>
