<x-layouts.public :title="__('Dashboard')">
    <div class="ui-container">
        <div class="mx-auto max-w-4xl space-y-4">
            <header class="space-y-2">
                <h1 class="font-sans text-4xl font-semibold tracking-tight text-zinc-900">{{ __('Dashboard') }}</h1>
                <p class="text-sm text-zinc-600">{{ __('Manage your account and moderation workflows from here.') }}</p>
            </header>

            <div class="grid gap-4 md:grid-cols-3">
                <x-ui.card>
                    <h2 class="text-base font-semibold text-zinc-900">{{ __('Content') }}</h2>
                    <p class="mt-1 text-sm text-zinc-600">{{ __('Review published posts and pending submissions.') }}</p>
                </x-ui.card>

                <x-ui.card>
                    <h2 class="text-base font-semibold text-zinc-900">{{ __('Community') }}</h2>
                    <p class="mt-1 text-sm text-zinc-600">{{ __('Moderate comments and community interactions.') }}</p>
                </x-ui.card>

                <x-ui.card>
                    <h2 class="text-base font-semibold text-zinc-900">{{ __('Newsletter') }}</h2>
                    <p class="mt-1 text-sm text-zinc-600">{{ __('Track subscriber growth and exports.') }}</p>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.public>
