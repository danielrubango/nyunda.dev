<x-layouts.public :title="__('Dashboard')">
    @php
        $isAdmin = auth()->user()?->hasRole(\App\Enums\UserRole::Admin) ?? false;
    @endphp

    <div class="ui-container">
        <div class="mx-auto max-w-4xl space-y-4">
            <header class="space-y-2">
                <h1 class="font-sans text-4xl font-semibold tracking-tight text-zinc-900">{{ __('Dashboard') }}</h1>
                <p class="text-sm text-zinc-600">{{ __('Manage your account and moderation workflows from here.') }}</p>
            </header>

            <div class="grid gap-4 md:grid-cols-1">
                <x-ui.card>
                    <h2 class="text-base font-semibold text-zinc-900">{{ __('Content') }}</h2>
                    <p class="mt-1 text-sm text-zinc-600">{{ __('Review published posts and pending submissions.') }}</p>
                    <a href="{{ route('dashboard.content.index') }}" class="mt-3 inline-flex border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 no-underline transition-colors hover:bg-zinc-100">
                        Voir mes contenus
                    </a>
                    <a href="{{ route('dashboard.content.create') }}" class="mt-2 inline-flex border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 no-underline transition-colors hover:bg-zinc-100">
                        Proposer un contenu
                    </a>
                    @if ($isAdmin)
                        <div class="mt-4 border-t border-zinc-200 pt-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Administration</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <a href="{{ \App\Filament\Resources\CommunityLinks\CommunityLinkResource::getUrl(panel: 'admin') }}" class="inline-flex border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 no-underline transition-colors hover:bg-zinc-100">
                                    Gerer la communaute (Filament)
                                </a>
                                <a href="{{ \App\Filament\Resources\NewsletterEditions\NewsletterEditionResource::getUrl(panel: 'admin') }}" class="inline-flex border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 no-underline transition-colors hover:bg-zinc-100">
                                    Gerer la newsletter (Filament)
                                </a>
                            </div>
                        </div>
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.public>
