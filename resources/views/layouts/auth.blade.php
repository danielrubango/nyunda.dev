<x-layouts.public :title="$title ?? null">
    <div class="ui-container">
        <div class="mx-auto flex min-h-[calc(100svh-15rem)] w-full max-w-4xl items-center justify-center py-6 sm:py-10">
            <div class="w-full max-w-md space-y-5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-zinc-900 no-underline">
                    <img src="{{ asset('nyunda-mark.svg') }}" alt="{{ config('app.name') }} logo" class="size-7" />
                    <span class="font-sans text-sm font-semibold tracking-[0.2em]">{{ strtoupper(config('app.name')) }}</span>
                </a>

                <x-ui.card class="p-6 sm:p-8">
                    {{ $slot }}
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.public>
